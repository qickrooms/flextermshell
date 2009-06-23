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
using Weborb.Client;


namespace FlexTermShell.src
{
    public interface ISSH2ClientService
    {
        AsyncToken<SilverLight.dao.SSH2ResultDAO> tryConnectThenExec(SilverLight.dao.SSH2DAO ssh2dao);
    }    
}
