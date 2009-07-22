using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Shapes;
using Weborb.Client;
using System.Collections;
using System.Diagnostics;
using System.Windows.Media.Effects;
using System.Windows.Browser;

namespace SilverLight
{
    public partial class MainPage : UserControl
    {
        public SilverLight.dao.SSH2DAO MySSH2DAO;
        public SilverLight.dao.SSH2ResultDAO MySSH2ResultDAO;
        private WeborbClient weborbClient;
        public FlexTermShell.src.ISSH2ClientService proxy;        
        
        public MainPage()
        {
            InitializeComponent();
            weborbClient = new WeborbClient("http://www.lookbackon.com/weborb/weborb.php", this);
            proxy = weborbClient.Bind<FlexTermShell.src.ISSH2ClientService>();
            

            MySSH2DAO = new SilverLight.dao.SSH2DAO();
            MySSH2ResultDAO = new SilverLight.dao.SSH2ResultDAO();

            //PopUpLoginTitleWindow;
            SilverLight.util.Dialog dlg = new MyDialog();
            dlg.Show(SilverLight.util.DialogStyle.Modal);
            dlg.mainPage = this;

        }

        private void ResultTB_KeyUp(object sender, KeyEventArgs e)
        {
            if (e.Key == Key.Enter)
            {
                MySSH2DAO.command = ResultTB.Text.Substring(ResultTB.Text.LastIndexOf(" "), (ResultTB.Text.Length - ResultTB.Text.LastIndexOf("$ ") - 1));
                //Debug.WriteLine(MySSH2DAO.command);
                AsyncToken<SilverLight.dao.SSH2ResultDAO> result = proxy.tryConnectThenExec(MySSH2DAO);
                
                result.ErrorListener += new ErrorHandler(result_ErrorListener);
                result.ResultListener += new ResponseHandler<SilverLight.dao.SSH2ResultDAO>(result_ResultListener);
                App.Current.RootVisual.Effect = new BlurEffect();

                this.Cursor = Cursors.Wait;                
            }
        }

        void result_ResultListener(SilverLight.dao.SSH2ResultDAO response)
        {
            try
            {
                if (MySSH2DAO.asMuchAsEver)
                {
                    siTB.Text = response.asMuchAsEver.ServerIdentification;
                    if (MySSH2DAO.command.IndexOf("pwd") != -1 || MySSH2DAO.command.IndexOf("cd") != -1)
                    {
                        cwdTB.Text = response.execResult;
                    }
                }
                
                ResultTB.Text += "\n";
                ResultTB.Text += response.execResult.ToString();
                ResultTB.Text += "\n";
                ResultTB.Text += "$ ";
                ResultTB.Focus();
                ResultTB.SelectionStart = ResultTB.Text.Length;                                

                this.Cursor = Cursors.Arrow;                
                App.Current.RootVisual.Effect = null;
            }
            catch (Exception e)
            {
                ResultTB.Text = e.ToString();
            }
        }

        void result_ErrorListener(Fault fault)
        {
            throw new NotImplementedException();
        }

        private void ResultTB_MouseLeftButtonUp(object sender, MouseButtonEventArgs e)
        {
            
            // add your code
            ResultTB.Text += "\n";
            ResultTB.Text += "###CompressionAlgorithmsClient2Server###:\n" + MySSH2ResultDAO.asMuchAsEver.CompressionAlgorithmsClient2Server;
            ResultTB.Text += "\n";
            ResultTB.Text += "###CompressionAlgorithmsServer2Client###:\n" + MySSH2ResultDAO.asMuchAsEver.CompressionAlgorithmsServer2Client;
            ResultTB.Text += "\n";
            ResultTB.Text += "###DebugInfo###:\n" + MySSH2ResultDAO.asMuchAsEver.DebugInfo;
            ResultTB.Text += "\n";
            ResultTB.Text += "###EncryptionAlgorithmsClient2Server###:\n" + MySSH2ResultDAO.asMuchAsEver.EncryptionAlgorithmsClient2Server;
            ResultTB.Text += "\n";
            ResultTB.Text += "###EncryptionAlgorithmsServer2Client###:\n" + MySSH2ResultDAO.asMuchAsEver.EncryptionAlgorithmsServer2Client;
            ResultTB.Text += "\n";
            ResultTB.Text += "###KexAlgorithms###:\n" + MySSH2ResultDAO.asMuchAsEver.KexAlgorithms;
            ResultTB.Text += "\n";
            ResultTB.Text += "###LanguagesClient2Server###:\n" + MySSH2ResultDAO.asMuchAsEver.LanguagesClient2Server;
            ResultTB.Text += "\n";
            ResultTB.Text += "###LanguagesServer2Client###:\n" + MySSH2ResultDAO.asMuchAsEver.LanguagesServer2Client;
            ResultTB.Text += "\n";
            ResultTB.Text += "###Log###:\n" + MySSH2ResultDAO.asMuchAsEver.Log;
            ResultTB.Text += "\n";
            ResultTB.Text += "###MACAlgorithmsClient2Server###:\n" + MySSH2ResultDAO.asMuchAsEver.MACAlgorithmsClient2Server;
            ResultTB.Text += "\n";
            ResultTB.Text += "###MACAlgorithmsServer2Client###:\n" + MySSH2ResultDAO.asMuchAsEver.MACAlgorithmsServer2Client;
            ResultTB.Text += "\n";
            ResultTB.Text += "###ServerHostKeyAlgorithms###:\n" + MySSH2ResultDAO.asMuchAsEver.ServerHostKeyAlgorithms;
            ResultTB.Text += "\n";
            ResultTB.Text += "###ServerIdentification###:\n" + MySSH2ResultDAO.asMuchAsEver.ServerIdentification;
            ResultTB.Text += "\n";
            //				ResultTB.Text += "ServerPublicHostKey:\n"+MySSH2ResultDAO.asMuchAsEver.ServerPublicHostKey.toString();//?byte array can not displayed?
            ResultTB.Text += "\n";
            ResultTB.Text += "$ ";
            ResultTB.Focus();
            ResultTB.SelectionStart = ResultTB.Text.Length;
               
            
        }



    }

    public class MyDialog : SilverLight.util.Dialog
    {
        private LoginTitleWindow ltw;
        protected override FrameworkElement GetContent()
        {

            // You could just use XamlReader to do everything except the event hookup.

            ltw = new LoginTitleWindow() { };
            //ltw.OKButton.Click += (sender, args) => { Close(); };
            ltw.OKButton.Click += new RoutedEventHandler(LoginBtn_Click);
            ltw.CancelButton.Click += new RoutedEventHandler(CancelButton_Click);
            return ltw;

        }

        void CancelButton_Click(object sender, RoutedEventArgs e)
        {
            ltw.hostTB.Text = "lookbackon.com";
            ltw.portTB.Text = "22";
            ltw.timeoutTB.Text = "10";
            ltw.usernameTB.Text = "godpaper";
            ltw.passwordBox.Password = "KiT7740321";
        }

        private void LoginBtn_Click(object sender, RoutedEventArgs e)
        {
            mainPage.MySSH2DAO.host = ltw.hostTB.Text;
            mainPage.MySSH2DAO.port = int.Parse(ltw.portTB.Text);
            mainPage.MySSH2DAO.timeout = int.Parse(ltw.timeoutTB.Text);
            mainPage.MySSH2DAO.username = ltw.usernameTB.Text;
            mainPage.MySSH2DAO.password = ltw.passwordBox.Password;

            AsyncToken<SilverLight.dao.SSH2ResultDAO> result = mainPage.proxy.tryConnectThenExec(mainPage.MySSH2DAO);
            
            result.ErrorListener += new ErrorHandler(result_ErrorListener);
            result.ResultListener += new ResponseHandler<SilverLight.dao.SSH2ResultDAO>(result_ResultListener);
            
            Close();
            
            mainPage.Cursor = Cursors.Wait;
        }

        
        
        void result_ResultListener(SilverLight.dao.SSH2ResultDAO response)
        {
            try
            {
                if (response.execResult != "Failure")
                {
                    if (mainPage.MySSH2DAO.asMuchAsEver)
                    {
                        mainPage.siTB.Text = response.asMuchAsEver.ServerIdentification;
                        if (mainPage.MySSH2DAO.command.IndexOf("pwd") != -1 || mainPage.MySSH2DAO.command.IndexOf("cd") != -1)
                        {
                            mainPage.cwdTB.Text = response.execResult;
                            mainPage.MySSH2ResultDAO.asMuchAsEver = response.asMuchAsEver;
                        }
                    }
                    mainPage.MySSH2ResultDAO.execResult = response.execResult;

                    mainPage.statusTB.Text = "CONNECTED";

                    mainPage.ResultTB.Text += "\n";
                    mainPage.ResultTB.Text = response.execResult.ToString();
                    mainPage.ResultTB.Text += "\n";
                    mainPage.ResultTB.Text += "$ ";
                    mainPage.ResultTB.Focus();
                    mainPage.ResultTB.SelectionStart = mainPage.ResultTB.Text.Length;
                    //mainPage.ResultTB.Select(mainPage.ResultTB.Text.LastIndexOf("$ ")+2, 1);
                }
                else
                {
                    HtmlPage.Window.Invoke("Login Failure!",response);
                }
                
            }
            catch (Exception e)
            {
                mainPage.ResultTB.Text = e.ToString();
                mainPage.ResultTB.Text += "$ ";
            }
            App.Current.RootVisual.Effect = null;
            mainPage.Cursor = Cursors.Arrow;            
        }

        void result_ErrorListener(Fault fault)
        {
            throw new NotImplementedException();
        }


        protected override void OnClickOutside()
        {

            //Close();

        }

    }

}
