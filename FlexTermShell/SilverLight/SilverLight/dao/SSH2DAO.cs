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
    public class SSH2DAO
    {
        private string _host = "lookbackon.com";
        public string host { get { return _host; } set { _host = value; } }

        private int _port = 22;
        public int port { get { return _port; } set { _port = value; } }

        private int _timeout = 10;
        public int timeout { get { return _timeout; } set { _timeout = value; } }

        private string _username = "godpaper";
        public string username { get { return _username; } set { _username = value; } }

        private string _password = "KiT7740321";
        public string password { get { return _password; } set { _password = value; } }

        private string _command = "pwd";
        public string command { get { return _command; } set { _command = value; } }

        private bool _asMuchAsEver = true;
        public bool asMuchAsEver { get { return _asMuchAsEver; } set { _asMuchAsEver = value; } }
    }
}
