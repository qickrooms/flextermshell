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
    public class SSH2ResultDAO
    {
        private string _execResult = "";
        public string execResult { get { return _execResult; } set { _execResult = value; } }

        private SSH2ResultDetailDAO _asMuchAsEver = new SSH2ResultDetailDAO();
        public SSH2ResultDetailDAO asMuchAsEver { get { return _asMuchAsEver; } set { _asMuchAsEver = value; } }
    }
}
