<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Pure-PHP implementation of SSHv1.
 *
 * PHP versions 4 and 5
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include('Net/SSH1.php');
 *
 *    $ssh = new Net_SSH1('www.domain.tld');
 *    if (!$ssh->login('username', 'password')) {
 *        exit('Login Failed');
 *    }
 *
 *    while (true) {
 *        echo $ssh->interactiveRead();
 *
 *        $read = array(STDIN);
 *        $write = $except = NULL;
 *        if (stream_select($read, $write, $except, 0)) {
 *            $ssh->interactiveWrite(fread(STDIN, 1));
 *        }
 *    }
 * ?>
 * </code>
 *
 * Here's another short example:
 * <code>
 * <?php
 *    include('Net/SSH1.php');
 *
 *    $ssh = new Net_SSH1('www.domain.tld');
 *    if (!$ssh->login('username', 'password')) {
 *        exit('Login Failed');
 *    }
 *
 *    echo $ssh->exec('ls -la');
 * ?>
 * </code>
 *
 * More information on the SSHv1 specification can be found by reading 
 * {@link http://www.snailbook.com/docs/protocol-1.5.txt protocol-1.5.txt}.
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA  02111-1307  USA
 *
 * @category   Net
 * @package    Net_SSH1
 * @author     Jim Wigginton <terrafrost@php.net>
 * @copyright  MMVII Jim Wigginton
 * @license    http://www.gnu.org/licenses/lgpl.txt
 * @version    $Id: SSH1.php,v 1.12 2009/06/09 04:00:38 terrafrost Exp $
 * @link       http://phpseclib.sourceforge.net
 */

/**
 * Include Math_BigInteger
 *
 * Used to do RSA encryption.
 */
require_once('../Math/BigInteger.php');

/**
 * Include Crypt_Null
 */
//require_once('Crypt/Null.php');

/**
 * Include Crypt_DES
 */
require_once('../Crypt/DES.php');

/**
 * Include Crypt_TripleDES
 */
require_once('../Crypt/TripleDES.php');

/**
 * Include Crypt_RC4
 */
require_once('../Crypt/RC4.php');

/**
 * Include Crypt_Random
 */
require_once('../Crypt/Random.php');

/**#@+
 * Protocol Flags
 *
 * @access private
 */
define('NET_SSH1_MSG_DISCONNECT',          1);
define('NET_SSH1_SMSG_PUBLIC_KEY',         2);
define('NET_SSH1_CMSG_SESSION_KEY',        3);
define('NET_SSH1_CMSG_USER',               4);
define('NET_SSH1_CMSG_AUTH_PASSWORD',      9);
define('NET_SSH1_CMSG_REQUEST_PTY',       10);
define('NET_SSH1_CMSG_EXEC_SHELL',        12);
define('NET_SSH1_CMSG_EXEC_CMD',          13);
define('NET_SSH1_SMSG_SUCCESS',           14);
define('NET_SSH1_SMSG_FAILURE',           15);
define('NET_SSH1_CMSG_STDIN_DATA',        16);
define('NET_SSH1_SMSG_STDOUT_DATA',       17);
define('NET_SSH1_SMSG_STDERR_DATA',       18);
define('NET_SSH1_SMSG_EXITSTATUS',        20);
define('NET_SSH1_CMSG_EXIT_CONFIRMATION', 33);
/**#@-*/

/**#@+
 * Encryption Methods
 *
 * @see Net_SSH1::getSupportedCiphers()
 * @access public
 */
/**
 * No encryption
 *
 * Not supported.
 */
define('NET_SSH1_CIPHER_NONE',       0);
/**
 * IDEA in CFB mode
 *
 * Not supported.
 */
define('NET_SSH1_CIPHER_IDEA',       1);
/**
 * DES in CBC mode
 */
define('NET_SSH1_CIPHER_DES',        2);
/**
 * Triple-DES in CBC mode
 *
 * All implementations are required to support this
 */
define('NET_SSH1_CIPHER_3DES',       3);
/**
 * TRI's Simple Stream encryption CBC
 *
 * Not supported nor is it defined in the official SSH1 specs.  OpenSSH, however, does define it (see cipher.h),
 * although it doesn't use it (see cipher.c)
 */
define('NET_SSH1_CIPHER_BROKEN_TSS', 4);
/**
 * RC4
 *
 * Not supported.
 *
 * @internal According to the SSH1 specs:
 *
 *        "The first 16 bytes of the session key are used as the key for
 *         the server to client direction.  The remaining 16 bytes are used
 *         as the key for the client to server direction.  This gives
 *         independent 128-bit keys for each direction."
 *
 *     This library currently only supports encryption when the same key is being used for both directions.  This is
 *     because there's only one $crypto object.  Two could be added ($encrypt and $decrypt, perhaps).
 */
define('NET_SSH1_CIPHER_RC4',        5);
/**
 * Blowfish
 *
 * Not supported nor is it defined in the official SSH1 specs.  OpenSSH, however, defines it (see cipher.h) and
 * uses it (see cipher.c)
 */
define('NET_SSH1_CIPHER_BLOWFISH',   6);
/**#@-*/

/**#@+
 * Authentication Methods
 *
 * @see Net_SSH1::getSupportedAuthentications()
 * @access public
 */
/**
 * .rhosts or /etc/hosts.equiv
 */
define('NET_SSH1_AUTH_RHOSTS',     1);
/**
 * pure RSA authentication
 */
define('NET_SSH1_AUTH_RSA',        2);
/**
 * password authentication
 *
 * This is the only method that is supported by this library.
 */
define('NET_SSH1_AUTH_PASSWORD',   3);
/**
 * .rhosts with RSA host authentication
 */
define('NET_SSH1_AUTH_RHOSTS_RSA', 4);
/**#@-*/

/**#@+
 * Terminal Modes
 *
 * @link http://3sp.com/content/developer/maverick-net/docs/Maverick.SSH.PseudoTerminalModesMembers.html
 * @access private
 */
define('NET_SSH1_TTY_OP_END',  0);
/**#@-*/

/**
 * The Response Type
 *
 * @see Net_SSH1::_get_binary_packet()
 * @access private
 */
define('NET_SSH1_RESPONSE_TYPE', 1);

/**
 * The Response Data
 *
 * @see Net_SSH1::_get_binary_packet()
 * @access private
 */
define('NET_SSH1_RESPONSE_DATA', 2);

/**#@+
 * Execution Bitmap Masks
 *
 * @see Net_SSH1::bitmap
 * @access private
 */
define('NET_SSH1_MASK_CONSTRUCTOR', 0x00000001);
define('NET_SSH1_MASK_LOGIN',       0x00000002);
define('NET_SSH1_MASK_SHELL',       0x00000004);
/**#@-*/

/**
 * Pure-PHP implementation of SSHv1.
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 * @version 0.1.0
 * @access  public
 * @package Net_SSH1
 */
class Net_SSH1 {
    /**
     * The SSH identifier
     *
     * @var String
     * @access private
     */
    var $identifier = 'SSH-1.5-phpseclib';

    /**
     * The Socket Object
     *
     * @var Object
     * @access private
     */
    var $fsock;

    /**
     * The cryptography object
     *
     * @var Object
     * @access private
     */
    var $crypto = false;

    /**
     * Execution Bitmap
     *
     * The bits that are set reprsent functions that have been called already.  This is used to determine
     * if a requisite function has been successfully executed.  If not, an error should be thrown.
     *
     * @var Integer
     * @access private
     */
    var $bitmap = 0;

    /**
     * The Server Key Public Exponent
     *
     * Logged for debug purposes
     *
     * @see Net_SSH1::getServerKeyPublicExponent()
     * @var String
     * @access private
     */
    var $server_key_public_exponent;

    /**
     * The Server Key Public Modulus
     *
     * Logged for debug purposes
     *
     * @see Net_SSH1::getServerKeyPublicModulus()
     * @var String
     * @access private
     */
    var $server_key_public_modulus;

    /**
     * The Host Key Public Exponent
     *
     * Logged for debug purposes
     *
     * @see Net_SSH1::getHostKeyPublicExponent()
     * @var String
     * @access private
     */
    var $host_key_public_exponent;

    /**
     * The Host Key Public Modulus
     *
     * Logged for debug purposes
     *
     * @see Net_SSH1::getHostKeyPublicModulus()
     * @var String
     * @access private
     */
    var $host_key_public_modulus;

    /**
     * Supported Ciphers
     *
     * Logged for debug purposes
     *
     * @see Net_SSH1::getSupportedCiphers()
     * @var Array
     * @access private
     */
    var $supported_ciphers = array(
        NET_SSH1_CIPHER_NONE       => 'No encryption',
        NET_SSH1_CIPHER_IDEA       => 'IDEA in CFB mode',
        NET_SSH1_CIPHER_DES        => 'DES in CBC mode',
        NET_SSH1_CIPHER_3DES       => 'Triple-DES in CBC mode',
        NET_SSH1_CIPHER_BROKEN_TSS => 'TRI\'s Simple Stream encryption CBC',
        NET_SSH1_CIPHER_RC4        => 'RC4',
        NET_SSH1_CIPHER_BLOWFISH   => 'Blowfish'
    );

    /**
     * Supported Authentications
     *
     * Logged for debug purposes
     *
     * @see Net_SSH1::getSupportedAuthentications()
     * @var Array
     * @access private
     */
    var $supported_authentications = array(
        NET_SSH1_AUTH_RHOSTS     => '.rhosts or /etc/hosts.equiv',
        NET_SSH1_AUTH_RSA        => 'pure RSA authentication',
        NET_SSH1_AUTH_PASSWORD   => 'password authentication',
        NET_SSH1_AUTH_RHOSTS_RSA => '.rhosts with RSA host authentication'
    );

    /**
     * Server Identification
     *
     * @see Net_SSH1::getServerIdentification()
     * @var String
     * @access private
     */
    var $server_identification = '';

    /**
     * Default Constructor.
     *
     * Connects to an SSHv1 server
     *
     * @param String $host
     * @param optional Integer $port
     * @param optional Integer $timeout
     * @param optional Integer $cipher
     * @return Net_SSH1
     * @access public
     */
    function Net_SSH1($host, $port = 22, $timeout = 10, $cipher = NET_SSH1_CIPHER_3DES)
    {
        $this->fsock = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$this->fsock) {
            user_error(rtrim("Cannot connect to $host. Error $errno. $errstr"), E_USER_NOTICE);
            return;
        }

        $this->server_identification = $init_line = fgets($this->fsock, 255);
        if (!preg_match('#SSH-([0-9\.]+)-(.+)#', $init_line, $parts)) {
            user_error('Can only connect to SSH servers', E_USER_NOTICE);
            return;
        }
        if ($parts[1][0] != 1) {
            user_error("Cannot connect to SSH $parts[1] servers", E_USER_NOTICE);
            return;
        }

        fputs($this->fsock, $this->identifier."\r\n");

        $response = $this->_get_binary_packet();
        if ($response[NET_SSH1_RESPONSE_TYPE] != NET_SSH1_SMSG_PUBLIC_KEY) {
            user_error('Expected SSH_SMSG_PUBLIC_KEY', E_USER_NOTICE);
            return;
        }

        $anti_spoofing_cookie = $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 8);

        $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 4);

        $temp = unpack('nlen', $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 2));
        $server_key_public_exponent = new Math_BigInteger($this->_string_shift($response[NET_SSH1_RESPONSE_DATA], ceil($temp['len'] / 8)), 256);
        $this->server_key_public_exponent = $server_key_public_exponent;

        $temp = unpack('nlen', $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 2));
        $server_key_public_modulus = new Math_BigInteger($this->_string_shift($response[NET_SSH1_RESPONSE_DATA], ceil($temp['len'] / 8)), 256);
        $this->server_key_public_modulus = $server_key_public_modulus;

        $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 4);

        $temp = unpack('nlen', $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 2));
        $host_key_public_exponent = new Math_BigInteger($this->_string_shift($response[NET_SSH1_RESPONSE_DATA], ceil($temp['len'] / 8)), 256);
        $this->host_key_public_exponent = $host_key_public_exponent;

        $temp = unpack('nlen', $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 2));
        $host_key_public_modulus = new Math_BigInteger($this->_string_shift($response[NET_SSH1_RESPONSE_DATA], ceil($temp['len'] / 8)), 256);
        $this->host_key_public_modulus = $host_key_public_modulus;

        $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 4);

        // get a list of the supported ciphers
        list(, $supported_ciphers_mask) = unpack('N', $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 4));
        foreach ($this->supported_ciphers as $mask=>$name) {
            if (($supported_ciphers_mask & (1 << $mask)) == 0) {
                unset($this->supported_ciphers[$mask]);
            }
        }

        // get a list of the supported authentications
        list(, $supported_authentications_mask) = unpack('N', $this->_string_shift($response[NET_SSH1_RESPONSE_DATA], 4));
        foreach ($this->supported_authentications as $mask=>$name) {
            if (($supported_authentications_mask & (1 << $mask)) == 0) {
                unset($this->supported_authentications[$mask]);
            }
        }

        $session_id = pack('H*', md5($host_key_public_modulus->toBytes() . $server_key_public_modulus->toBytes() . $anti_spoofing_cookie));

        $session_key = '';
        for ($i = 0; $i < 32; $i++) {
            $session_key.= chr(crypt_random(0, 255));
        }
        $double_encrypted_session_key = $session_key ^ str_pad($session_id, 32, chr(0));

        if ($server_key_public_modulus->compare($host_key_public_modulus) < 0) {
            $double_encrypted_session_key = $this->_rsa_crypt(
                $double_encrypted_session_key,
                array(
                    $server_key_public_exponent,
                    $server_key_public_modulus
                )
            );
            $double_encrypted_session_key = $this->_rsa_crypt(
                $double_encrypted_session_key,
                array(
                    $host_key_public_exponent,
                    $host_key_public_modulus
                )
            );
        } else {
            $double_encrypted_session_key = $this->_rsa_crypt(
                $double_encrypted_session_key,
                array(
                    $host_key_public_exponent,
                    $host_key_public_modulus
                )
            );
            $double_encrypted_session_key = $this->_rsa_crypt(
                $double_encrypted_session_key,
                array(
                    $server_key_public_exponent,
                    $server_key_public_modulus
                )
            );
        }

        $cipher = isset($this->supported_ciphers[$cipher]) ? $cipher : NET_SSH1_CIPHER_3DES;
        $data = pack('C2a*na*N', NET_SSH1_CMSG_SESSION_KEY, $cipher, $anti_spoofing_cookie, 8 * strlen($double_encrypted_session_key), $double_encrypted_session_key, 0);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_SESSION_KEY', E_USER_NOTICE);
            return;
        }

        switch ($cipher) {
            //case NET_SSH1_CIPHER_NONE:
            //    $this->crypto = new Crypt_Null();
            //    break;
            case NET_SSH1_CIPHER_DES:
                $this->crypto = new Crypt_DES();
                $this->crypto->disablePadding();
                $this->crypto->enableContinuousBuffer();
                $this->crypto->setKey(substr($session_key, 0,  8));
                break;
            case NET_SSH1_CIPHER_3DES:
                $this->crypto = new Crypt_TripleDES(CRYPT_DES_MODE_3CBC);
                $this->crypto->disablePadding();
                $this->crypto->enableContinuousBuffer();
                $this->crypto->setKey(substr($session_key, 0, 24));
                break;
            //case NET_SSH1_CIPHER_RC4:
            //    $this->crypto = new Crypt_RC4();
            //    $this->crypto->enableContinuousBuffer();
            //    $this->crypto->setKey(substr($session_key, 0,  16));
            //    break;
        }

        $response = $this->_get_binary_packet();

        if ($response[NET_SSH1_RESPONSE_TYPE] != NET_SSH1_SMSG_SUCCESS) {
            user_error('Expected SSH_SMSG_SUCCESS', E_USER_NOTICE);
            return;
        }

        $this->bitmap = NET_SSH1_MASK_CONSTRUCTOR;
    }

    /**
     * Login
     *
     * @param String $username
     * @param optional String $password
     * @return Boolean
     * @access public
     */
    function login($username, $password = '')
    {
        if (!($this->bitmap & NET_SSH1_MASK_CONSTRUCTOR)) {
            return false;
        }

        $data = pack('CNa*', NET_SSH1_CMSG_USER, strlen($username), $username);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_USER', E_USER_NOTICE);
            return false;
        }

        $response = $this->_get_binary_packet();

        if ($response[NET_SSH1_RESPONSE_TYPE] == NET_SSH1_SMSG_SUCCESS) {
            $this->bitmap |= NET_SSH1_MASK_LOGIN;
            return true;
        } else if ($response[NET_SSH1_RESPONSE_TYPE] != NET_SSH1_SMSG_FAILURE) {
            user_error('Expected SSH_SMSG_SUCCESS or SSH_SMSG_FAILURE', E_USER_NOTICE);
            return false;
        }

        $data = pack('CNa*', NET_SSH1_CMSG_AUTH_PASSWORD, strlen($password), $password);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_AUTH_PASSWORD', E_USER_NOTICE);
            return false;
        }

        $response = $this->_get_binary_packet();

        if ($response[NET_SSH1_RESPONSE_TYPE] == NET_SSH1_SMSG_SUCCESS) {
            $this->bitmap |= NET_SSH1_MASK_LOGIN;
            return true;
        } else if ($response[NET_SSH1_RESPONSE_TYPE] == NET_SSH1_SMSG_FAILURE) {
            return false;
        } else {
            user_error('Expected SSH_SMSG_SUCCESS or SSH_SMSG_FAILURE', E_USER_NOTICE);
            return false;
        }
    }

    /**
     * Executes a command on a non-interactive shell, returns the output, and quits.
     *
     * An SSH1 server will close the connection after a command has been executed on a non-interactive shell.  SSH2
     * servers don't, however, this isn't an SSH2 client.  The way this works, on the server, is by initiating a
     * shell with the -s option, as discussed in the following links:
     *
     * {@link http://www.faqs.org/docs/bashman/bashref_65.html http://www.faqs.org/docs/bashman/bashref_65.html}
     * {@link http://www.faqs.org/docs/bashman/bashref_62.html http://www.faqs.org/docs/bashman/bashref_65.html}
     *
     * To execute further commands, a new Net_SSH1 object will need to be created.
     *
     * Returns false on failure and the output, otherwise.
     *
     * @see Net_SSH1::interactiveRead()
     * @see Net_SSH1::interactiveWrite()
     * @param String $cmd
     * @return mixed
     * @access public
     */
    function exec($cmd)
    {
        if (!($this->bitmap & NET_SSH1_MASK_LOGIN)) {
            user_error('Operation disallowed prior to login()', E_USER_NOTICE);
            return false;
        }

        // connect using the sample parameters in protocol-1.5.txt.
        // according to wikipedia.org's entry on text terminals, "the fundamental type of application running on a text
        // terminal is a command line interpreter or shell".  thus, opening a terminal session to run the shell.
        $data = pack('CNa*N4C', NET_SSH1_CMSG_REQUEST_PTY, strlen('vt100'), 'vt100', 24, 80, 0, 0, NET_SSH1_TTY_OP_END);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_REQUEST_PTY', E_USER_NOTICE);
            return false;
        }

        $response = $this->_get_binary_packet();

        if ($response[NET_SSH1_RESPONSE_TYPE] != NET_SSH1_SMSG_SUCCESS) {
            user_error('Expected SSH_SMSG_SUCCESS', E_USER_NOTICE);
            return false;
        }

        $data = pack('CNa*', NET_SSH1_CMSG_EXEC_CMD, strlen($cmd), $cmd);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_EXEC_CMD', E_USER_NOTICE);
            return false;
        }

        $output = '';
        $response = $this->_get_binary_packet();

        do {
            $output.= substr($response[NET_SSH1_RESPONSE_DATA], 4);
            $response = $this->_get_binary_packet();
        } while ($response[NET_SSH1_RESPONSE_TYPE] != NET_SSH1_SMSG_EXITSTATUS);

        $data = pack('C', NET_SSH1_CMSG_EXIT_CONFIRMATION);

        // i don't think it's really all that important if this packet gets sent or not.
        $this->_send_binary_packet($data);

        fclose($this->fsock);

        // reset the execution bitmap - a new Net_SSH1 object needs to be created.
        $this->bitmap = 0;

        return $output;
    }

    /**
     * Creates an interactive shell
     *
     * @see Net_SSH1::interactiveRead()
     * @see Net_SSH1::interactiveWrite()
     * @return Boolean
     * @access private
     */
    function _initShell()
    {
        $data = pack('CNa*N4C', NET_SSH1_CMSG_REQUEST_PTY, strlen('vt100'), 'vt100', 24, 80, 0, 0, NET_SSH1_TTY_OP_END);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_REQUEST_PTY', E_USER_NOTICE);
            return false;
        }

        $response = $this->_get_binary_packet();

        if ($response[NET_SSH1_RESPONSE_TYPE] != NET_SSH1_SMSG_SUCCESS) {
            user_error('Expected SSH_SMSG_SUCCESS', E_USER_NOTICE);
            return false;
        }

        $data = pack('C', NET_SSH1_CMSG_EXEC_SHELL);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_EXEC_SHELL', E_USER_NOTICE);
            return false;
        }

        $this->bitmap |= NET_SSH1_MASK_SHELL;

        //stream_set_blocking($this->fsock, 0);

        return true;
    }

    /**
     * Inputs a command into an interactive shell.
     *
     * @see Net_SSH1::interactiveRead()
     * @param String $cmd
     * @return Boolean
     * @access public
     */
    function interactiveWrite($cmd)
    {
        if (!($this->bitmap & NET_SSH1_MASK_LOGIN)) {
            user_error('Operation disallowed prior to login()', E_USER_NOTICE);
            return false;
        }

        if (!($this->bitmap & NET_SSH1_MASK_SHELL) && !$this->_initShell()) {
            user_error('Unable to initiate an interactive shell session', E_USER_NOTICE);
            return false;
        }

        $data = pack('CNa*', NET_SSH1_CMSG_STDIN_DATA, strlen($cmd), $cmd);

        if (!$this->_send_binary_packet($data)) {
            user_error('Error sending SSH_CMSG_STDIN', E_USER_NOTICE);
            return false;
        }

        return true;
    }

    /**
     * Reads the output of an interactive shell.
     *
     * Requires PHP 4.3.0 or later due to the use of the stream_select() function.  If you see stuff like
     * "[00m", you're seeing ANSI escape codes.  According to
     * {@link http://support.microsoft.com/kb/101875 How to Enable ANSI.SYS in a Command Window}, "Windows NT
     * does not support ANSI escape sequences in Win32 Console applications", so if you're a Windows user,
     * there's not going to be much recourse.
     *
     * @see Net_SSH1::interactiveRead()
     * @return String
     * @access public
     */
    function interactiveRead()
    {
        if (!($this->bitmap & NET_SSH1_MASK_LOGIN)) {
            user_error('Operation disallowed prior to login()', E_USER_NOTICE);
            return false;
        }

        if (!($this->bitmap & NET_SSH1_MASK_SHELL) && !$this->_initShell()) {
            user_error('Unable to initiate an interactive shell session', E_USER_NOTICE);
            return false;
        }

        $read = array($this->fsock);
        $write = $except = null;
        if (stream_select($read, $write, $except, 0)) {
            $response = $this->_get_binary_packet();
            return substr($response[NET_SSH1_RESPONSE_DATA], 4);
        } else {
            return '';
        }
    }

    /**
     * Disconnect
     *
     * @access public
     */
    function disconnect()
    {
        $this->_disconnect();
    }

    /**
     * Destructor.
     *
     * Will be called, automatically, if you're supporting just PHP5.  If you're supporting PHP4, you'll need to call
     * disconnect().
     *
     * @access public
     */
    function __destruct()
    {
        $this->_disconnect();
    }

    /**
     * Disconnect
     *
     * @param String $msg
     * @access private
     */
    function _disconnect($msg = 'Client Quit')
    {
        if ($this->bitmap) {
            $data = pack('CNa*', NET_SSH1_MSG_DISCONNECT, strlen($msg), $msg);
            $this->_send_binary_packet($data);
            fclose($this->fsock);
            $this->bitmap = 0;
        }
    }

    /**
     * Gets Binary Packets
     *
     * See 'The Binary Packet Protocol' of protocol-1.5.txt for more info.
     *
     * Also, this function could be improved upon by adding detection for the following exploit:
     * http://www.securiteam.com/securitynews/5LP042K3FY.html
     *
     * @see Net_SSH1::_send_binary_packet()
     * @return Array
     * @access private
     */
    function _get_binary_packet()
    {
        if (feof($this->fsock)) {
            //user_error('connection closed prematurely', E_USER_NOTICE);
            return false;
        }

        $temp = unpack('Nlength', fread($this->fsock, 4));

        $padding_length = 8 - ($temp['length'] & 7);
        $length = $temp['length'] + $padding_length;

        $raw = fread($this->fsock, $length);

        if ($this->crypto !== false) {
            $raw = $this->crypto->decrypt($raw);
        }

        $padding = substr($raw, 0, $padding_length);
        $type = $raw[$padding_length];
        $data = substr($raw, $padding_length + 1, -4);

        $temp = unpack('Ncrc', substr($raw, -4));

        //if ( $temp['crc'] != $this->_crc($padding . $type . $data) ) {
        //    user_error('Bad CRC in packet from server', E_USER_NOTICE);
        //    return false;
        //}

        return array(
            NET_SSH1_RESPONSE_TYPE => ord($type),
            NET_SSH1_RESPONSE_DATA => $data
        );
    }

    /**
     * Sends Binary Packets
     *
     * Returns true on success, false on failure.
     *
     * @see Net_SSH1::_get_binary_packet()
     * @param String $data
     * @return Boolean
     * @access private
     */
    function _send_binary_packet($data) {
        if (feof($this->fsock)) {
            //user_error('connection closed prematurely', E_USER_NOTICE);
            return false;
        }

        $length = strlen($data) + 4;

        $padding_length = 8 - ($length & 7);
        $padding = '';
        for ($i = 0; $i < $padding_length; $i++) {
            $padding.= chr(crypt_random(0, 255));
        }

        $data = $padding . $data;
        $data.= pack('N', $this->_crc($data));

        if ($this->crypto !== false) {
            $data = $this->crypto->encrypt($data);
        }

        $packet = pack('Na*', $length, $data);

        return strlen($packet) == fputs($this->fsock, $packet);
    }

    /**
     * Cyclic Redundancy Check (CRC)
     *
     * PHP's crc32 function is implemented slightly differently than the one that SSH v1 uses, so
     * we've reimplemented it. A more detailed discussion of the differences can be found after
     * $crc_lookup_table's initialization.
     *
     * @see Net_SSH1::_get_binary_packet()
     * @see Net_SSH1::_send_binary_packet()
     * @param String $data
     * @return Integer
     * @access private
     */
    function _crc($data)
    {
        static $crc_lookup_table = array(
            0x00000000, 0x77073096, 0xEE0E612C, 0x990951BA,
            0x076DC419, 0x706AF48F, 0xE963A535, 0x9E6495A3,
            0x0EDB8832, 0x79DCB8A4, 0xE0D5E91E, 0x97D2D988,
            0x09B64C2B, 0x7EB17CBD, 0xE7B82D07, 0x90BF1D91,
            0x1DB71064, 0x6AB020F2, 0xF3B97148, 0x84BE41DE,
            0x1ADAD47D, 0x6DDDE4EB, 0xF4D4B551, 0x83D385C7,
            0x136C9856, 0x646BA8C0, 0xFD62F97A, 0x8A65C9EC,
            0x14015C4F, 0x63066CD9, 0xFA0F3D63, 0x8D080DF5,
            0x3B6E20C8, 0x4C69105E, 0xD56041E4, 0xA2677172,
            0x3C03E4D1, 0x4B04D447, 0xD20D85FD, 0xA50AB56B,
            0x35B5A8FA, 0x42B2986C, 0xDBBBC9D6, 0xACBCF940,
            0x32D86CE3, 0x45DF5C75, 0xDCD60DCF, 0xABD13D59,
            0x26D930AC, 0x51DE003A, 0xC8D75180, 0xBFD06116,
            0x21B4F4B5, 0x56B3C423, 0xCFBA9599, 0xB8BDA50F,
            0x2802B89E, 0x5F058808, 0xC60CD9B2, 0xB10BE924,
            0x2F6F7C87, 0x58684C11, 0xC1611DAB, 0xB6662D3D,
            0x76DC4190, 0x01DB7106, 0x98D220BC, 0xEFD5102A,
            0x71B18589, 0x06B6B51F, 0x9FBFE4A5, 0xE8B8D433,
            0x7807C9A2, 0x0F00F934, 0x9609A88E, 0xE10E9818,
            0x7F6A0DBB, 0x086D3D2D, 0x91646C97, 0xE6635C01,
            0x6B6B51F4, 0x1C6C6162, 0x856530D8, 0xF262004E,
            0x6C0695ED, 0x1B01A57B, 0x8208F4C1, 0xF50FC457,
            0x65B0D9C6, 0x12B7E950, 0x8BBEB8EA, 0xFCB9887C,
            0x62DD1DDF, 0x15DA2D49, 0x8CD37CF3, 0xFBD44C65,
            0x4DB26158, 0x3AB551CE, 0xA3BC0074, 0xD4BB30E2,
            0x4ADFA541, 0x3DD895D7, 0xA4D1C46D, 0xD3D6F4FB,
            0x4369E96A, 0x346ED9FC, 0xAD678846, 0xDA60B8D0,
            0x44042D73, 0x33031DE5, 0xAA0A4C5F, 0xDD0D7CC9,
            0x5005713C, 0x270241AA, 0xBE0B1010, 0xC90C2086,
            0x5768B525, 0x206F85B3, 0xB966D409, 0xCE61E49F,
            0x5EDEF90E, 0x29D9C998, 0xB0D09822, 0xC7D7A8B4,
            0x59B33D17, 0x2EB40D81, 0xB7BD5C3B, 0xC0BA6CAD,
            0xEDB88320, 0x9ABFB3B6, 0x03B6E20C, 0x74B1D29A,
            0xEAD54739, 0x9DD277AF, 0x04DB2615, 0x73DC1683,
            0xE3630B12, 0x94643B84, 0x0D6D6A3E, 0x7A6A5AA8,
            0xE40ECF0B, 0x9309FF9D, 0x0A00AE27, 0x7D079EB1,
            0xF00F9344, 0x8708A3D2, 0x1E01F268, 0x6906C2FE,
            0xF762575D, 0x806567CB, 0x196C3671, 0x6E6B06E7,
            0xFED41B76, 0x89D32BE0, 0x10DA7A5A, 0x67DD4ACC,
            0xF9B9DF6F, 0x8EBEEFF9, 0x17B7BE43, 0x60B08ED5,
            0xD6D6A3E8, 0xA1D1937E, 0x38D8C2C4, 0x4FDFF252,
            0xD1BB67F1, 0xA6BC5767, 0x3FB506DD, 0x48B2364B,
            0xD80D2BDA, 0xAF0A1B4C, 0x36034AF6, 0x41047A60,
            0xDF60EFC3, 0xA867DF55, 0x316E8EEF, 0x4669BE79,
            0xCB61B38C, 0xBC66831A, 0x256FD2A0, 0x5268E236,
            0xCC0C7795, 0xBB0B4703, 0x220216B9, 0x5505262F,
            0xC5BA3BBE, 0xB2BD0B28, 0x2BB45A92, 0x5CB36A04,
            0xC2D7FFA7, 0xB5D0CF31, 0x2CD99E8B, 0x5BDEAE1D,
            0x9B64C2B0, 0xEC63F226, 0x756AA39C, 0x026D930A,
            0x9C0906A9, 0xEB0E363F, 0x72076785, 0x05005713,
            0x95BF4A82, 0xE2B87A14, 0x7BB12BAE, 0x0CB61B38,
            0x92D28E9B, 0xE5D5BE0D, 0x7CDCEFB7, 0x0BDBDF21,
            0x86D3D2D4, 0xF1D4E242, 0x68DDB3F8, 0x1FDA836E,
            0x81BE16CD, 0xF6B9265B, 0x6FB077E1, 0x18B74777,
            0x88085AE6, 0xFF0F6A70, 0x66063BCA, 0x11010B5C,
            0x8F659EFF, 0xF862AE69, 0x616BFFD3, 0x166CCF45,
            0xA00AE278, 0xD70DD2EE, 0x4E048354, 0x3903B3C2,
            0xA7672661, 0xD06016F7, 0x4969474D, 0x3E6E77DB,
            0xAED16A4A, 0xD9D65ADC, 0x40DF0B66, 0x37D83BF0,
            0xA9BCAE53, 0xDEBB9EC5, 0x47B2CF7F, 0x30B5FFE9,
            0xBDBDF21C, 0xCABAC28A, 0x53B39330, 0x24B4A3A6,
            0xBAD03605, 0xCDD70693, 0x54DE5729, 0x23D967BF,
            0xB3667A2E, 0xC4614AB8, 0x5D681B02, 0x2A6F2B94,
            0xB40BBE37, 0xC30C8EA1, 0x5A05DF1B, 0x2D02EF8D
        );

        // For this function to yield the same output as PHP's crc32 function, $crc would have to be
        // set to 0xFFFFFFFF, initially - not 0x00000000 as it currently is.
        $crc = 0x00000000;
        $length = strlen($data);

        for ($i=0;$i<$length;$i++) {
            // We AND $crc >> 8 with 0x00FFFFFF because we want the eight newly added bits to all
            // be zero.  PHP, unfortunately, doesn't always do this.  0x80000000 >> 8, as an example,
            // yields 0xFF800000 - not 0x00800000.  The following link elaborates:
            // http://www.php.net/manual/en/language.operators.bitwise.php#57281
            $crc = (($crc >> 8) & 0x00FFFFFF) ^ $crc_lookup_table[($crc & 0xFF) ^ ord($data[$i])];
        }

        // In addition to having to set $crc to 0xFFFFFFFF, initially, the return value must be XOR'd with
        // 0xFFFFFFFF for this function to return the same thing that PHP's crc32 function would.
        return $crc;
    }

    /**
     * String Shift
     *
     * Inspired by array_shift
     *
     * @param String $string
     * @param optional Integer $index
     * @return String
     * @access private
     */
    function _string_shift(&$string, $index = 1)
    {
        $substr = substr($string, 0, $index);
        $string = substr($string, $index);
        return $substr;
    }

    /**
     * RSA Encrypt
     *
     * Returns mod(pow($m, $e), $n), where $n should be the product of two (large) primes $p and $q and where $e
     * should be a number with the property that gcd($e, ($p - 1) * ($q - 1)) == 1.  Could just make anything that
     * calls this call modexp, instead, but I think this makes things clearer, maybe...
     *
     * @see Net_SSH1::Net_SSH1()
     * @param Math_BigInteger $m
     * @param Array $key
     * @return Math_BigInteger
     * @access private
     */
    function _rsa_crypt($m, $key)
    {
        // To quote from protocol-1.5.txt:
        // The most significant byte (which is only partial as the value must be
        // less than the public modulus, which is never a power of two) is zero.
        //
        // The next byte contains the value 2 (which stands for public-key
        // encrypted data in the PKCS standard [PKCS#1]).  Then, there are non-
        // zero random bytes to fill any unused space, a zero byte, and the data
        // to be encrypted in the least significant bytes, the last byte of the
        // data in the least significant byte.

        // Presumably the part of PKCS#1 they're refering to is "Section 7.2.1 Encryption Operation",
        // under "7.2 RSAES-PKCS1-v1.5" and "7 Encryption schemes" of the following URL:
        // ftp://ftp.rsasecurity.com/pub/pkcs/pkcs-1/pkcs-1v2-1.pdf
        $temp = chr(0) . chr(2);
        $modulus = $key[1]->toBytes();
        $length = strlen($modulus) - strlen($m) - 3;
        for ($i = 0; $i < $length; $i++) {
            $temp.= chr(crypt_random(1, 255));
        }
        $temp.= chr(0) . $m;

        $m = new Math_BigInteger($temp, 256);
        $m = $m->modPow($key[0], $key[1]);

        return $m->toBytes();
    }

    /**
     * Return the server key public exponent
     *
     * Returns, by default, the base-10 representation.  If $raw_output is set to true, returns, instead,
     * the raw bytes.  This behavior is similar to PHP's md5() function.
     *
     * @param optional Boolean $raw_output
     * @return String
     * @access public
     */
    function getServerKeyPublicExponent($raw_output = false)
    {
        return $raw_output ? $this->server_key_public_exponent->toBytes() : $this->server_key_public_exponent->toString();
    }

    /**
     * Return the server key public modulus
     *
     * Returns, by default, the base-10 representation.  If $raw_output is set to true, returns, instead,
     * the raw bytes.  This behavior is similar to PHP's md5() function.
     *
     * @param optional Boolean $raw_output
     * @return String
     * @access public
     */
    function getServerKeyPublicModulus($raw_output = false)
    {
        return $raw_output ? $this->server_key_public_modulus->toBytes() : $this->server_key_public_modulus->toString();
    }

    /**
     * Return the host key public exponent
     *
     * Returns, by default, the base-10 representation.  If $raw_output is set to true, returns, instead,
     * the raw bytes.  This behavior is similar to PHP's md5() function.
     *
     * @param optional Boolean $raw_output
     * @return String
     * @access public
     */
    function getHostKeyPublicExponent($raw_output = false)
    {
        return $raw_output ? $this->host_key_public_exponent->toBytes() : $this->host_key_public_exponent->toString();
    }

    /**
     * Return the host key public modulus
     *
     * Returns, by default, the base-10 representation.  If $raw_output is set to true, returns, instead,
     * the raw bytes.  This behavior is similar to PHP's md5() function.
     *
     * @param optional Boolean $raw_output
     * @return String
     * @access public
     */
    function getHostKeyPublicModulus($raw_output = false)
    {
        return $raw_output ? $this->host_key_public_modulus->toBytes() : $this->host_key_public_modulus->toString();
    }

    /**
     * Return a list of ciphers supported by SSH1 server.
     *
     * Just because a cipher is supported by an SSH1 server doesn't mean it's supported by this library. If $raw_output
     * is set to true, returns, instead, an array of constants.  ie. instead of array('Triple-DES in CBC mode'), you'll
     * get array(NET_SSH1_CIPHER_3DES).
     *
     * @param optional Boolean $raw_output
     * @return Array
     * @access public
     */
    function getSupportedCiphers($raw_output = false)
    {
        return $raw_output ? array_keys($this->supported_ciphers) : array_values($this->supported_ciphers);
    }

    /**
     * Return a list of authentications supported by SSH1 server.
     *
     * Just because a cipher is supported by an SSH1 server doesn't mean it's supported by this library. If $raw_output
     * is set to true, returns, instead, an array of constants.  ie. instead of array('password authentication'), you'll
     * get array(NET_SSH1_AUTH_PASSWORD).
     *
     * @param optional Boolean $raw_output
     * @return Array
     * @access public
     */
    function getSupportedAuthentications($raw_output = false)
    {
        return $raw_output ? array_keys($this->supported_authentications) : array_values($this->supported_authentications);
    }

    /**
     * Return the server identification.
     *
     * @return String
     * @access public
     */
    function getServerIdentification()
    {
        return rtrim($this->server_identification);
    }
}
?>