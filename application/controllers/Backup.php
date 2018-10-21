<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

error_reporting(-1);
ini_set('display_errors', 1);
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

        $content = "<?php \n";

        $content .= '$dbhost = "'.$dbhost.'"; '." \n";
        $content .= '$dbuser = "'.$dbuser.'"; '." \n";
        $content .= '$dbpass = "'.$dbpass.'"; '." \n";
        $content .= '$dbname = "'.$dbname.'"; '." \n";

        $fp = fopen('./uploads/'. "dbconfig.php","w+");

        fwrite($fp,$content);

        fclose($fp);

		$backup_file = 'database.sql';
		
		$command = "mysqldump -u $dbuser -p$dbpass $dbname > $backup_file";

        my_var_dump('running command: '.$command);
        system($command);

        $download_link = base_url().$backup_file;

		# Now email this backup file
		$this->load->library('email');

		$this->email->clear(TRUE);
		$this->email->set_mailtype("html");
		$this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
        $this->email->to('admin@pref.menu');

        $hostname = gethostname();
        $environment = ENVIRONMENT;
        $datetime = date('r e');
		# prepare message here
		$message =	"The database backup file is attached.
                    <br>Date: $datetime
                    <br>Host: $hostname
                    <br>Environment: $environment
					<br>$download_link
					<br>Thanks
					<br>".__FILE__;
		$this->email->subject(SYSTEM_NAME." database backup");
		$this->email->message($message);
		
		//$this->email->attach("./$backup_file");
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
        $backukp_files_and_directories[] = 'database.sql';
        $backukp_files_and_directories[] = 'thumb.php';

        $backup_file = 'site_backup.zip';
        $command = "zip -r $backup_file ".implode(' ',$backukp_files_and_directories);
        system($command);

        echo $command.'<br>\n';

        $download_link = base_url().$backup_file;

        # Now email this backup file
        $this->load->library('email');

        $this->email->clear(TRUE);
        $this->email->set_mailtype("html");
        $this->email->from(SYSTEM_EMAIL, SYSTEM_NAME);
        $this->email->to('admin@pref.menu');

        $hostname = gethostname();
        $environment = ENVIRONMENT;
        $datetime = date('r e');
        # prepare message here
        $message =	"The zip backup file is attached.
                    <br>Date: $datetime
                    <br>Host: $hostname
                    <br>Environment: $environment
					<br>$download_link
					<br>Thanks
					<br>".__FILE__;
        $this->email->subject(SYSTEM_NAME." zip backup");
        $this->email->message($message);

        //$this->email->attach("./$backup_file");
        $this->email->send();
	}
}