using System;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Documents;
using System.Windows.Ink;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Shapes;

using Weborb.Client;

//namespace Weborb.Examples
namespace FlexTermShell.src
{
    public interface IBasicService
    {
        AsyncToken<int> Calculate( int arg1, int op, int arg2 );
    }
}
