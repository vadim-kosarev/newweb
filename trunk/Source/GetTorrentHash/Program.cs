﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.IO;

using System.ComponentModel;
using System.Data;

using MonoTorrent.Common;
using MonoTorrent;

namespace GetTorrentHash
{
    class Program
    {
        /***************************************************************************************************/
        /* Main                                                                                            */
        /***************************************************************************************************/
        static void Debug(Object o)
        {
            Out("[DEBUG]", o);
        }

        static void Trace(Object o)
        {
            Out("[TRACE]", o);
        }

        static void Out(String prefix, Object arg)
        {
            Console.WriteLine(prefix + " " + arg);
        }

        /***************************************************************************************************/
        /* Main                                                                                            */
        /***************************************************************************************************/
        [STAThreadAttribute]
        static void Main(string[] args)
        {
            try
            {
                // =======================================================================================================
                if (args.Length < 1)
                {
                    Usage(args);
                    return;
                }
                else
                {
                    Run(args);
                }
                // =======================================================================================================
            }
            catch (Exception e)
            {
                UserMessage(e.Message, "ERROR");
            }
        }

        static String CreateTorrentFile(String argPath)
        {

            // BitComet --make <SOURCE> [--output=OUTPUT] [--silent] [--tray] 

            String dstTorrentDir = "C:\\.torrents"; // @TODO: get tempDir

            // create tempDir:
            FileInfo info = new FileInfo(dstTorrentDir);

            if (File.Exists(dstTorrentDir))
                File.Delete(dstTorrentDir);

            if (Directory.Exists(dstTorrentDir))
            {
                // clean directory
                string[] files = Directory.GetFiles(dstTorrentDir, "*");
                foreach ( string file in files )
                {
                    File.Delete(file);
                }
            }

            if (info.Exists && !Directory.Exists(dstTorrentDir))
            {
                throw new Exception(dstTorrentDir + " exists and not a directory");
            }

            Directory.CreateDirectory(dstTorrentDir);

            System.Diagnostics.Process proc = new System.Diagnostics.Process();
            proc.StartInfo.FileName = GetBitcometPath();
            proc.EnableRaisingEvents = false;
            proc.StartInfo.Arguments = "--make \"" + argPath + "\" --output=" + dstTorrentDir + " --silent --tray";

            Debug("Exe: " + proc.StartInfo.FileName + " " + proc.StartInfo.Arguments);

            System.IO.FileSystemWatcher fsWatcher = new System.IO.FileSystemWatcher(dstTorrentDir);

            int timeOutSec = 60; // @TODO: config

            proc.Start();

            System.IO.WaitForChangedResult res =
                fsWatcher.WaitForChanged(System.IO.WatcherChangeTypes.Created, timeOutSec * 1000);

            String dstTorrentPath = Path.Combine(dstTorrentDir, res.Name);

            if (dstTorrentPath.Length == 0)
            {
                throw new Exception("Failed to create .torrent-file: " + dstTorrentPath);
            }

            return dstTorrentPath;
        }

        /**
         * returns path to BitComet.exe
         */
        static String GetBitcometPath()
        {
            String exeFile = "BitComet.exe";

            Microsoft.Win32.RegistryKey userKey =
                Microsoft.Win32.Registry.CurrentUser.OpenSubKey("Software\\BitComet");

            Object dfltVal = userKey.GetValue("");

            if (null == dfltVal)
            {
                throw new Exception("Can't read registry Software\\Bitcomet");
            }

            String installPath = dfltVal.ToString();

            if (installPath.Length == 0)
            {
                throw new Exception("Can't get installation path for BitComet");
            }

            return installPath + "\\" + exeFile;
        }

        /**
         * builds magnet URI
         */
        static String BuildMagnetUri(String pathToTorrentFile)
        {
            Debug("Processing " + pathToTorrentFile);

            Torrent torrentFile = Torrent.Load(pathToTorrentFile);
            Debug("torrentFile: " + torrentFile);

            InfoHash infoHash = torrentFile.InfoHash;
            string hexHash = infoHash.ToHex();
            Debug("hexHash: " + hexHash);

            // Magent link:
            // magnet:?xt=urn:btih:YXHGOZXR67FFCDMCRBPO4U4NNXDDNFBN

            // magnet:?xl=[Размер в байтах]&dn=[Имя файла (URL encoded)]&xt=urn:tree:tiger:[ TTH хеш  файла (Base32) ]

            // dn (Display Name) — Имя файла
            // xl (eXact Length) — Размер файла в байтах

            // xt (eXact Topic) — URN, содержащий хеш файла
            // as (Acceptable Source) — Веб-ссылка на файл в Интернете
            // xs (eXact Source) — P2P ссылка
            // kt (Keyword Topic) — Ключевые слова для поиска
            // mt (Manifest Topic) — Ссылка на метафайл, который содержит список магнетов (MAGMA)
            // tr (TRacker) — Адрес трекера для BitTorrent клиентов.

            // magnet:?xt=urn:btih:YXHGOZXR67FFCDMCRBPO4U4NNXDDNFBN&tr=
            // http://freetorrent.ru/announce.php


            //String announceUrl = "http://freetorrent.ru/announce.php";
            /**
             * http://10.rarbg.com/announce
               http://9.rarbg.com:2710/announce
             */

            String announceUrl = "http://9.rarbg.com:2710/announce";

            String anUrlEncoded = System.Web.HttpUtility.UrlEncode(announceUrl);

            String magnetUrl = " magnet:?xt=urn:btih:" + hexHash +"&tr=" + anUrlEncoded;

            return magnetUrl;
        }

        static void CopyToClipboard(String text)
        {
            System.Windows.Forms.Clipboard.SetText(text);
        }

        static void AlertUser(String text)
        {
            UserMessage(text, "Copied to Clipboard!");
        }

        static void UserMessage(String text, String caption)
        {
            System.Windows.Forms.MessageBox.Show(text, caption);
        }

        /***************************************************************************************************/
        /* Run                                                                                             */
        /***************************************************************************************************/
        static void Run(string[] args)
        {
            String argPath = args[0];

            // 1. create .torrent file with BitComet's command line interface and make BitComet seed the content
            String pathToTorrentFile = CreateTorrentFile(argPath);

            // 2. Build magnet url from resuling .torrent-file
            String magnetUri = BuildMagnetUri(pathToTorrentFile);

            // 3. Copy magnet URL to ClipBoard and inform user about that
            CopyToClipboard(magnetUri);

            // 4. Remove temporary .torrent file and exit
            AlertUser(magnetUri);
            return;

        }

        /***************************************************************************************************/
        /* Usage                                                                                           */
        /***************************************************************************************************/
        static void Usage(string[] args)
        {
            Console.WriteLine("Usage: <Program.exe> \"Path-to-file-to-share\"");
        }

    }
}