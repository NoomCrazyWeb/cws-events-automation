<?php

class CWS_Plugin_Updater {

	// Defining constants
	private const PLUGIN_SLUG = 'cws-events-automation';
	private const UPDATE_URL = 'https://iz.crazywebstudio.dev/update.json';
	private const PLUGIN_FILE = 'cws-events-automation/cws-events-automation.php';

	public function __construct() {

		// Hook into WordPress filters
		add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
		add_filter('site_transient_update_plugins', [$this, 'push_update']);
	}

	/**
	 * Fetch plugin information for display on the Plugin Details page
	 */
	public function plugin_info($res, $action, $args) {
		if (!$this->is_valid_plugin_page($action, $args)) {
			return $res;
		}

		$plugin_info = $this->fetch_remote_plugin_info();

		if ($plugin_info) {
			return $this->populate_plugin_info($plugin_info);
		}

		return $res;
	}

	/**
	 * Push the plugin update to the transient system
	 */
	public function push_update($transient) {
		if (!$this->is_plugins_page() || empty($transient->checked)) {
			return $transient;
		}

		$plugin_info = $this->fetch_remote_plugin_info();

		if ($plugin_info && $this->is_update_available($transient->checked[self::PLUGIN_FILE], $plugin_info['new_version'])) {
			$transient = $this->populate_transient_with_update($transient, $plugin_info);
		}

		return $transient;
	}

	/**
	 * Helper method to check if we are on the plugins page
	 */
	private function is_plugins_page() {
		$screen = get_current_screen();
		return ($screen && $screen->id === 'plugins');
	}

	/**
	 * Helper method to validate if the current page action matches the plugin slug
	 */
	private function is_valid_plugin_page($action, $args) {
		return $action === 'plugin_information' && isset($args->slug) && $args->slug === self::PLUGIN_SLUG;
	}

	/**
	 * Fetch remote plugin information from the update URL
	 */
	private function fetch_remote_plugin_info() {
		$response = wp_remote_get(self::UPDATE_URL);

		if ($this->is_valid_response($response)) {
			$remote_data = json_decode(wp_remote_retrieve_body($response), true);
			return $remote_data[self::PLUGIN_SLUG][0] ?? null; // Return the latest update info
		}

		return null;
	}

	/**
	 * Check if a valid HTTP response is returned
	 */
	private function is_valid_response($response) {
		return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
	}

	/**
	 * Compare installed plugin version with the latest available version
	 */
	private function is_update_available($current_version, $new_version) {
		return version_compare($current_version, $new_version, '<');
	}

	/**
	 * Populate plugin information for display on the Plugin Details page
	 */
	private function populate_plugin_info($plugin_info) {
		$res = new stdClass();
		$res->name = $plugin_info['name'];
		$res->slug = $plugin_info['slug'];
		$res->version = $plugin_info['new_version'];
		$res->author = $plugin_info['author'];
		$res->author_profile = $plugin_info['author_profile'];
		$res->requires = $plugin_info['requires'];
		$res->tested = $plugin_info['tested'];
		$res->last_updated = $plugin_info['last_updated'];
		$res->homepage = $plugin_info['homepage'];
		$res->download_link = $plugin_info['download_link'];
		$res->requires_php = $plugin_info['requires_php'];
		$res->banners = array(
			'low' => $plugin_info['banners']['low'],
			'high' => $plugin_info['banners']['high']
		);
		// Populate the sections from the JSON file
		if (isset($plugin_info['sections'])) {
			$res->sections = $plugin_info['sections'];
		}

		return $res;
	}

	/**
	 * Populate the transient with plugin update details
	 */
	private function populate_transient_with_update($transient, $plugin_info) {
		$transient->response[self::PLUGIN_FILE] = (object) [
			'new_version' => $plugin_info['new_version'],
			'package'     => $plugin_info['package'],
			'slug'        => self::PLUGIN_SLUG,
		];

		return $transient;
	}
}

// Initialize the updater class
if (is_admin()) {
	new CWS_Plugin_Updater();
}
