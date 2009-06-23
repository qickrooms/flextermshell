<?php
/*******************************************************************
 * BasicService.php
 * Copyright (C) 2006-2007 Midnight Coders, LLC
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * The Original Code is WebORB Presentation Server (R) for PHP.
 * 
 * The Initial Developer of the Original Code is Midnight Coders, LLC.
 * All Rights Reserved.
 ********************************************************************/
class BasicService
{
    var $ADD = 1;
    var $SUBTRACT = 2;
    var $MULTIPLY = 3;
    var $DIVIDE = 4;

    // Flex client invokes the Calculate method to demonstrate
    // the use of RemoteObject to connect with PHP.
    // Notice the data type Flex passes into the remote invocation
    // is string, but the arguments in the Calculate method are
    // integers. WebORB performs the conversion from String to int
    // before it dispatches the invocation
    public function Calculate($arg1, $op, $arg2 )
    {
        switch( $op )
        {
            case $this->ADD:
                return $arg1 + $arg2;

            case $this->SUBTRACT:
                return $arg1 - $arg2;

            case $this->MULTIPLY:
                return $arg1 * $arg2;

            case $this->DIVIDE:
            	{
            		if($arg2 == 0)
            			throw new Exception("Division by zero");
                	return $arg1 / $arg2;
            	}

            default:
                throw new Exception( "unknown operation" );
        }
    }
}
?>