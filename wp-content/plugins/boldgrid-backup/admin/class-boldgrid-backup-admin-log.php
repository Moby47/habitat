<?php
/**
 * File: class-boldgrid-backup-admin-log.php
 *
 * @link  https://www.boldgrid.com
 * @since 1.12.5
 *
 * @package    Boldgrid_Backup
 * @subpackage Boldgrid_Backup/admin
 * @copyright  BoldGrid
 * @version    $Id$
 * @author     BoldGrid <support@boldgrid.com>
 */

/**
 * Class: Boldgrid_Backup_Admin_Log
 *
 * @since 1.12.5
 */
class Boldgrid_Backup_Admin_Log {
	/**
	 * The core class object.
	 *
	 * @since  1.10.0
	 * @access private
	 * @var    Boldgrid_Backup_Admin_Core
	 */
	private $core;

	/**
	 * Log file filename.
	 *
	 * @since 1.12.5
	 * @var string
	 * @access private
	 */
	private $filename;

	/**
	 * Log file filepath.
	 *
	 * @since 1.12.5
	 * @var string
	 * @access private
	 */
	private $filepath;

	/**
	 * Constructor.
	 *
	 * @since 1.10.1
	 *
	 * @param Boldgrid_Backup_Admin_Core $core Boldgrid_Backup_Admin_Core object.
	 */
	public function __construct( Boldgrid_Backup_Admin_Core $core ) {
		$this->core = $core;
	}

	/**
	 * Add a message to the log.
	 *
	 * @since 1.12.5
	 *
	 * @param string $message The message to add to the log.
	 */
	public function add( $message ) {
		// Add a timestamp to the message.
		$message = date( '[Y-m-d H:i:s e]' ) . ' ' . $message;

		/*
		 * Append the message to the log.
		 *
		 * WP_Filesystem does not have a way to append to a file, so we're rewriting the file each
		 * time. Best route would be to fopen the file and append. This may need to be revisited.
		 */
		$file_content  = $this->core->wp_filesystem->get_contents( $this->filepath );
		$file_content .= PHP_EOL . $message;
		$this->core->wp_filesystem->put_contents( $this->filepath, $file_content );
	}

	/**
	 * Add generic info for all logs.
	 *
	 * @since 1.12.6
	 */
	public function add_generic() {
		$this->add( 'PHP Version: ' . phpversion() );

		$this->add( 'WordPress Version: ' . get_bloginfo( 'version' ) );

		$this->add( 'Total Upkeep version: ' . BOLDGRID_BACKUP_VERSION );
	}

	/**
	 * Add info to the log about memory usage.
	 *
	 * @since 1.12.5
	 */
	public function add_memory() {
		$limit       = ini_get( 'memory_limit' );
		$memory      = memory_get_usage();
		$memory_peak = memory_get_peak_usage();

		$message = sprintf(
			'Memory usage - limit / current / peak memory usage: %1$s / %2$s (%3$s) / %4$s (%5$s)',
			$limit,
			$memory,
			size_format( $memory, 2 ),
			$memory_peak,
			size_format( $memory_peak )
		);

		$this->add( $message );
	}

	/**
	 * Delete old log files.
	 *
	 * @since 1.12.5
	 */
	public function clean_up() {
		// Get a dirlist of our logs dir.
		$logs_dir = $this->core->backup_dir->get_logs_dir();
		$dirlist  = $this->core->wp_filesystem->dirlist( $logs_dir );

		foreach ( $dirlist as $item ) {
			// Skip if this is not a log file.
			if ( 'log' !== pathinfo( $item['name'], PATHINFO_EXTENSION ) ) {
				continue;
			}

			// Skip if this file is not old enough to delete.
			$is_too_old = time() - $item['lastmodunix'] > $this->core->configs['max_log_age'];
			if ( ! $is_too_old ) {
				continue;
			}

			$filepath = Boldgrid_Backup_Admin_Utility::trailingslashit( $logs_dir ) . $item['name'];

			$this->core->wp_filesystem->delete( $filepath );
		}
	}

	/**
	 * Init.
	 *
	 * @since 1.12.5
	 *
	 * @param  string $filename The filename of the log to create.
	 * @return bool             Whether or not the log file was created successfully.
	 */
	public function init( $filename ) {
		// Purging of old log files is done here, when we're creating a new one.
		$this->clean_up();

		$this->filename = sanitize_file_name( $filename );

		$this->filepath = $this->core->backup_dir->get_logs_dir() . DIRECTORY_SEPARATOR . $this->filename;

		$this->init_signal_handler();

		$log_created = $this->core->wp_filesystem->touch( $this->filepath );

		if ( $log_created ) {
			$this->add_generic();
		}

		return $log_created;
	}

	/**
	 * Add signal handlers.
	 *
	 * The one signal we can't handle is SIGFILL (kill -9).
	 *
	 * @since 1.12.6
	 */
	private function init_signal_handler() {
		/*
		 * Available only on php >= 7.1
		 *
		 * With PHP 7.1, pcntl_async_signals was added to enable asynchronous signal handling, and it
		 * works great.
		 *
		 * Using ticks in php 5.6 not working as expected.
		 */
		if ( ! version_compare( phpversion(), '7.1', '>=' ) ) {
			return;
		}

		if ( ! function_exists( 'pcntl_async_signals' ) ) {
			$this->add( 'Cannot add signal handlers, pcntl_async_signals function does not exist.' );
			return;
		}

		// Enable asynchronous signal handling.
		pcntl_async_signals( true );

		$signals = [
			// Ctrl+C.
			SIGINT,
			// Ctrl+\ (similiar to SIGINT, generates a core dump if necessary).
			SIGQUIT,
			// kill.
			SIGTERM,
			// Terminal log-out.
			SIGHUP,
			/*
			 * Apache graceful stop.
			 *
			 * @link https://stackoverflow.com/questions/780853/what-is-in-apache-2-a-caught-sigwinch-error
			 */
			SIGWINCH,
		];

		foreach ( $signals as $signal ) {
			pcntl_signal( $signal, [ $this, 'signal_handler' ] );
		}
	}

	/**
	 * Signal handler.
	 *
	 * @since 1.12.6
	 *
	 * @param int The signal being handled.
	 */
	public function signal_handler( $signo ) {
		$this->add( 'Signal received: ' . $signo );

		exit;
	}
}
