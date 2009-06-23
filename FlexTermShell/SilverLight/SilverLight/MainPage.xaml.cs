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

namespace SilverLight
{
    public partial class MainPage : UserControl
    {
        public SilverLight.dao.SSH2DAO MySSH2DAO;
        private WeborbClient weborbClient;
        public FlexTermShell.src.ISSH2ClientService proxy;
        //private Weborb.Examples.IBasicService proxy2;
        private FlexTermShell.src.IBasicService proxy2;   

        public MainPage()
        {
            InitializeComponent();
            weborbClient = new WeborbClient("http://localhost/weborb36/weborb.php", this);
            proxy = weborbClient.Bind<FlexTermShell.src.ISSH2ClientService>();
            //proxy2 = weborbClient.Bind<Weborb.Examples.IBasicService>();
            proxy2 = weborbClient.Bind<FlexTermShell.src.IBasicService>();

            MySSH2DAO = new SilverLight.dao.SSH2DAO();           

            //PopUpLoginTitleWindow;
            SilverLight.util.Dialog dlg = new MyDialog();
            dlg.Show(SilverLight.util.DialogStyle.Modal);
            dlg.mainPage = this;           
            
        }

        private void ResultTB_KeyUp(object sender, KeyEventArgs e)
        {
            if (e.Key == Key.Enter)
            {
                MySSH2DAO.command = ResultTB.Text.Substring(ResultTB.Text.LastIndexOf(" "), (ResultTB.Text.Length - ResultTB.Text.LastIndexOf("$ ")-1));
                Debug.WriteLine(MySSH2DAO.command);
                AsyncToken<SilverLight.dao.SSH2ResultDAO> result = proxy.tryConnectThenExec(MySSH2DAO);
                //AsyncToken<int> result = proxy2.Calculate(3, 1, 7);
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

                ResultTB.Text = response.execResult.ToString();
                ResultTB.Text += "\n";
                ResultTB.Text += "$ ";
                
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

        
        
    }

    public class MyDialog:SilverLight.util.Dialog
    {
        private LoginTitleWindow ltw;
        protected override FrameworkElement GetContent()
        {

            // You could just use XamlReader to do everything except the event hookup.

            ltw = new LoginTitleWindow() {};
            //ltw.OKButton.Click += (sender, args) => { Close(); };
            ltw.OKButton.Click += new RoutedEventHandler(LoginBtn_Click);
            ltw.CancelButton.Click += new RoutedEventHandler(CancelButton_Click);
            return ltw;         

        }

        void CancelButton_Click(object sender, RoutedEventArgs e)
        {
            ltw.hostTB.Text = "";
            ltw.portTB.Text = "22";
            ltw.timeoutTB.Text = "10";
            ltw.usernameTB.Text = "";
            ltw.passwordBox.Password = "";
        }

        private void LoginBtn_Click(object sender, RoutedEventArgs e)
        {
            mainPage.MySSH2DAO.host = ltw.hostTB.Text;
            mainPage.MySSH2DAO.port = int.Parse(ltw.portTB.Text);
            mainPage.MySSH2DAO.timeout = int.Parse(ltw.timeoutTB.Text);
            mainPage.MySSH2DAO.username = ltw.usernameTB.Text;
            mainPage.MySSH2DAO.password = ltw.passwordBox.Password;            

            AsyncToken<SilverLight.dao.SSH2ResultDAO> result = mainPage.proxy.tryConnectThenExec(mainPage.MySSH2DAO);
            //AsyncToken<int> result = proxy2.Calculate(3, 1, 7);
            result.ErrorListener += new ErrorHandler(result_ErrorListener);
            result.ResultListener += new ResponseHandler<SilverLight.dao.SSH2ResultDAO>(result_ResultListener);
            //result.ResultListener += new ResponseHandler<int>(result_ResultListener);
            Close();
            mainPage.Cursor= Cursors.Wait;
        }

        void result_ResultListener(SilverLight.dao.SSH2ResultDAO response)
        {
            try
            {
                if (mainPage.MySSH2DAO.asMuchAsEver)
                {
                    mainPage.siTB.Text = response.asMuchAsEver.ServerIdentification;
                    if (mainPage.MySSH2DAO.command.IndexOf("pwd") != -1 || mainPage.MySSH2DAO.command.IndexOf("cd") != -1)
                    {
                        mainPage.cwdTB.Text = response.execResult;
                    }                   
                }
                App.Current.RootVisual.Effect = null;
                mainPage.ResultTB.Text = response.execResult.ToString();
                mainPage.ResultTB.Text += "\n";
                mainPage.ResultTB.Text += "$ ";
                mainPage.ResultTB.Focus();
                //mainPage.ResultTB.SelectionStart = mainPage.ResultTB.Text.Length - 1;
                //mainPage.ResultTB.Select(mainPage.ResultTB.Text.LastIndexOf("$ ")+2, 1);
            }
            catch (Exception e)
            {
                mainPage.ResultTB.Text = e.ToString();
                mainPage.ResultTB.Text += "$ ";
            }
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
