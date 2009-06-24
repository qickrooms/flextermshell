using System;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Documents;
using System.Windows.Ink;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Shapes;


namespace SilverLight.dao
{    
    public class SSH2ResultDetailDAO
    {
        private string _Log = "";
        public string Log { get { return _Log; } set { _Log = value; } }//String

        private string _DebugInfo = "";
        public string DebugInfo { get { return _DebugInfo; } set { _DebugInfo = value; } }//String

        private string _ServerIdentification = "";
        public string ServerIdentification { get { return _ServerIdentification; } set { _ServerIdentification = value; } }//String

        private string _KexAlgorithms = "";
        public string KexAlgorithms { get { return _KexAlgorithms; } set { _KexAlgorithms = value; } }//Array

        private string _ServerHostKeyAlgorithms = "";
        public string ServerHostKeyAlgorithms { get { return _ServerHostKeyAlgorithms; } set { _ServerHostKeyAlgorithms = value; } }//Array

        private string _EncryptionAlgorithmsClient2Server = "";
        public string EncryptionAlgorithmsClient2Server { get { return _EncryptionAlgorithmsClient2Server; } set { _EncryptionAlgorithmsClient2Server = value; } }//Array

        private string _EncryptionAlgorithmsServer2Client = "";
        public string EncryptionAlgorithmsServer2Client { get { return _EncryptionAlgorithmsServer2Client; } set { _EncryptionAlgorithmsServer2Client = value; } }//Array

        private string _MACAlgorithmsClient2Server = "";
        public string MACAlgorithmsClient2Server { get { return _MACAlgorithmsClient2Server; } set { _MACAlgorithmsClient2Server = value; } }//Array

        private string _MACAlgorithmsServer2Client = "";
        public string MACAlgorithmsServer2Client { get { return _MACAlgorithmsServer2Client; } set { _MACAlgorithmsServer2Client = value; } }//Array

        private string _CompressionAlgorithmsClient2Server = "";
        public string CompressionAlgorithmsClient2Server { get { return _CompressionAlgorithmsClient2Server; } set { _CompressionAlgorithmsClient2Server = value; } }//Array

        private string _CompressionAlgorithmsServer2Client = "";
        public string CompressionAlgorithmsServer2Client { get { return _CompressionAlgorithmsServer2Client; } set { _CompressionAlgorithmsServer2Client = value; } }//Array

        private string _LanguagesServer2Client = "";
        public string LanguagesServer2Client { get { return _LanguagesServer2Client; } set { _LanguagesServer2Client = value; } }//Array

        private string _LanguagesClient2Server = "";
        public string LanguagesClient2Server { get { return _LanguagesClient2Server; } set { _LanguagesClient2Server = value; } }//Array

        private string _ServerPublicHostKey = "";
        public string ServerPublicHostKey { get { return _ServerPublicHostKey; } set { _ServerPublicHostKey = value; } }//Array
    }
}
