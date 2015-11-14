<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backup extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function database()
	{
		$dbhost = $this->db->hostname;
		$dbuser = $this->db->username;
		$dbpass = $this->db->password;
		$dbname = $this->db->database;
		
		$backup_dir = "";
		
		/*if( ! is_dir($backup_dir) ) //create the folder if it's not already exists
		{
			my_var_dump('Creating directory: '.$backup_dir);
			mkdir($backup_dir,0755,TRUE);
		}*/
		
		//self::delete_older_files($backup_dir);
		
		$backup_file = 'site_backup.sql';
		
		$command = "mysqldump -u $dbuser -p$dbpass $dbname > $backup_file";
		system($command);
		
		echo $command.'<br>\n';
		
		# Now email this backup file
		$this->load->library('email');

		$this->email->clear(TRUE);
		$this->email->set_mailtype("html");
		$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
		$this->email->to('admin@pref.menu');
		//$this->email->to('volcanock@gmail.com');
		//$this->email->cc('volcano_ck@yahoo.com');
		//$this->email->bcc('them@their-example.com');
		
		# prepare message here
		$message =	"The database backup file is attached.
					<br>$backup_file
					<br>Thanks
					<br>".__FILE__;
		$this->email->subject("Database Backup $backup_file");
		$this->email->message($message);
		
		$this->email->attach("./$backup_file");
		$this->email->send();
	}
	
	public function files_zip()
	{
		$backukp_files_and_directories[] = 'application/';
		$backukp_files_and_directories[] = 'css/';
		$backukp_files_and_directories[] = 'fonts/';
		$backukp_files_and_directories[] = 'images/';
		$backukp_files_and_directories[] = 'js/';
		$backukp_files_and_directories[] = 'phpmailer/';
		$backukp_files_and_directories[] = 'system/';
		$backukp_files_and_directories[] = 'uploads/';
		$backukp_files_and_directories[] = '.htaccess';
		$backukp_files_and_directories[] = 'composer.json';
		$backukp_files_and_directories[] = 'crons.php';
		$backukp_files_and_directories[] = 'error.log';
		$backukp_files_and_directories[] = 'index.php';
		$backukp_files_and_directories[] = 'movie.mp4';
		$backukp_files_and_directories[] = 'php.ini';
		$backukp_files_and_directories[] = 'phpinfo.php';
		$backukp_files_and_directories[] = 'site_backup.sql';
		$backukp_files_and_directories[] = 'thumb.php';
		
		$backup_file = 'site_backup.zip';
		$command = "zip -r $backup_file ".implode(' ',$backukp_files_and_directories);
		system($command);
		
		echo $command.'<br>\n';
	}
	
	/*public function delete_older_files($dir)
	{
		// cycle through all files in the directory
		foreach (glob($dir."/*.sql") as $file) 
		{
			// if file is 7 days (86400*7 seconds) old then delete it
			if (filemtime($file) < time() - (86400*7)) 
			{
				my_var_dump('Deleting '.$file);
				delete_file($file);
			}
		}
	}*/
}