using System.Diagnostics;
using System.Net.Sockets;
using System.Text;
using System.Windows.Forms;

internal static class Program
{
    private static readonly string BaseDir = AppContext.BaseDirectory.TrimEnd('\\');
    private static readonly string DataDir = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), "PerpustakaanGarbaSastra", "Data");
    private static readonly string MariaDir = Path.Combine(BaseDir, "mariadb");
    private static readonly string PhpDir = Path.Combine(BaseDir, "php");
    private static readonly string AppDir = Path.Combine(BaseDir, "app");
    private const int DbPort = 3307;
    private const int WebPort = 8087;
    private static Process? db;
    private static Process? php;

    [STAThread]
    private static int Main()
    {
        try
        {
            Directory.CreateDirectory(DataDir);
            InitializeDatabaseIfNeeded();
            StartDatabase();
            if (!WaitForPort(DbPort, 30000)) throw new Exception("Database lokal gagal dijalankan.");
            ImportSchema();
            StartPhp();
            if (!WaitForPort(WebPort, 15000)) throw new Exception("Web server lokal gagal dijalankan.");
            Process.Start(new ProcessStartInfo($"http://127.0.0.1:{WebPort}/") { UseShellExecute = true });
            while (!(php?.HasExited ?? true)) Thread.Sleep(500);
            return 0;
        }
        catch (Exception ex)
        {
            MessageBox.Show("Perpustakaan tidak dapat dijalankan.\n\n" + ex.Message, "Perpustakaan", MessageBoxButtons.OK, MessageBoxIcon.Error);
            return 1;
        }
        finally
        {
            Stop(php);
            Stop(db);
        }
    }

    private static void InitializeDatabaseIfNeeded()
    {
        var data = Path.Combine(DataDir, "mysql");
        if (Directory.Exists(data) && Directory.GetFileSystemEntries(data).Length > 0) return;
        var installer = FindMaria("mariadb-install-db.exe", "mysql_install_db.exe");
        if (installer == null) throw new Exception("Program inisialisasi MariaDB tidak ditemukan.");
        Run(installer, $"--datadir=\"{data}\" --password=\"\"");
    }

    private static void StartDatabase()
    {
        var server = FindMaria("mariadbd.exe", "mysqld.exe");
        if (server == null) throw new Exception("Server MariaDB tidak ditemukan.");
        db = Process.Start(new ProcessStartInfo
        {
            FileName = server,
            WorkingDirectory = MariaDir,
            Arguments = $"--datadir=\"{Path.Combine(DataDir, "mysql")}\" --port={DbPort} --bind-address=127.0.0.1 --skip-name-resolve --max-connections=50 --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci",
            UseShellExecute = false,
            CreateNoWindow = true
        });
    }

    private static void ImportSchema()
    {
        var client = FindMaria("mariadb.exe", "mysql.exe");
        if (client == null) throw new Exception("Client MariaDB tidak ditemukan.");
        var sql = Path.Combine(AppDir, "offline", "database.sql");
        if (!File.Exists(sql)) throw new Exception("File database.sql tidak ditemukan.");
        Run(client, $"-h127.0.0.1 -P{DbPort} -uroot --protocol=tcp", sql);
    }

    private static void StartPhp()
    {
        var exe = Path.Combine(PhpDir, "php.exe");
        if (!File.Exists(exe)) throw new Exception("PHP portable tidak ditemukan.");
        php = Process.Start(new ProcessStartInfo
        {
            FileName = exe,
            WorkingDirectory = AppDir,
            Arguments = $"-S 127.0.0.1:{WebPort} -t \"{AppDir}\"",
            UseShellExecute = false,
            CreateNoWindow = true
        });
    }

    private static string? FindMaria(params string[] names)
    {
        foreach (var n in names)
        {
            var p = Path.Combine(MariaDir, "bin", n);
            if (File.Exists(p)) return p;
            p = Path.Combine(MariaDir, n);
            if (File.Exists(p)) return p;
        }
        return null;
    }

    private static void Run(string exe, string args, string? stdinFile = null)
    {
        var psi = new ProcessStartInfo(exe, args)
        {
            WorkingDirectory = Path.GetDirectoryName(exe) ?? BaseDir,
            UseShellExecute = false,
            CreateNoWindow = true,
            RedirectStandardInput = stdinFile != null,
            RedirectStandardOutput = true,
            RedirectStandardError = true,
            StandardOutputEncoding = Encoding.UTF8,
            StandardErrorEncoding = Encoding.UTF8
        };
        using var p = Process.Start(psi) ?? throw new Exception($"Gagal menjalankan {Path.GetFileName(exe)}.");
        if (stdinFile != null)
        {
            p.StandardInput.Write(File.ReadAllText(stdinFile, Encoding.UTF8));
            p.StandardInput.Close();
        }
        if (!p.WaitForExit(60000))
        {
            try { p.Kill(true); } catch { }
            throw new Exception($"{Path.GetFileName(exe)} timeout.");
        }
        if (p.ExitCode != 0)
            throw new Exception($"{Path.GetFileName(exe)} gagal: {p.StandardError.ReadToEnd().Trim()}");
    }

    private static bool WaitForPort(int port, int timeoutMs)
    {
        var end = DateTime.UtcNow.AddMilliseconds(timeoutMs);
        while (DateTime.UtcNow < end)
        {
            try
            {
                using var c = new TcpClient();
                var t = c.ConnectAsync("127.0.0.1", port);
                if (t.Wait(300) && c.Connected) return true;
            }
            catch { }
            Thread.Sleep(250);
        }
        return false;
    }

    private static void Stop(Process? p)
    {
        if (p == null) return;
        try { if (!p.HasExited) p.Kill(true); } catch { }
        p.Dispose();
    }
}
