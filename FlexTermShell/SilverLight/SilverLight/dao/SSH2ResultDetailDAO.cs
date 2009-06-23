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
        public string Log { get; set; }//String
        public string DebugInfo { get; set; }//String
        public string ServerIdentification { get; set; }//String
        public string KexAlgorithms { get; set; }//Array
        public string ServerHostKeyAlgorithms { get; set; }//Array
        public string EncryptionAlgorithmsClient2Server { get; set; }//Array
        public string EncryptionAlgorithmsServer2Client { get; set; }//Array
        public string MACAlgorithmsClient2Server { get; set; }//Array
        public string MACAlgorithmsServer2Client { get; set; }//Array
        public string CompressionAlgorithmsClient2Server { get; set; }//Array
        public string CompressionAlgorithmsServer2Client { get; set; }//Array
        public string LanguagesServer2Client { get; set; }//Array
        public string LanguagesClient2Server { get; set; }//Array
        public string ServerPublicHostKey { get; set; }//Array
    }
}
