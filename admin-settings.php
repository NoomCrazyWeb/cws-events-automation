<?php
class Cws_Events_Automation_Admin_Settings {

    private $options;

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'page_init']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'CWS Submissions integrate to Events',  // Page title
            'CWS Submissions',      // Menu title
            'manage_options',          // Capability
            'cws-ninja-forms-config',  // Menu slug
            [$this, 'create_admin_page'], // Function to display the page
            'dashicons-admin-generic', // Icon URL
            110                         // Position
        );
    }

    public function create_admin_page() {
        $this->options = get_option('cws_ninja_forms_config');
        ?>
        <div class="wrap">
            <h1>CWS Submissions integrate to Events</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cws_ninja_forms_group');
                do_settings_sections('cws-ninja-forms-config');
                submit_button();

                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        // General Data Section
        register_setting(
            'cws_ninja_forms_group', // Option group
            'cws_ninja_forms_config', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            'general_data_section', // ID
            'General Data', // Title
            [$this, 'print_general_data_info'], // Callback
            'cws-ninja-forms-config' // Page
        );

        add_settings_field(
            'form_id', // ID
            'Form ID (*)', // Title
            [$this, 'form_id_callback'], // Callback
            'cws-ninja-forms-config', // Page
            'general_data_section' // Section
        );

        add_settings_field(
            'organizer', // ID
            'Organizer ID (multiple use ,)', // Title
            [$this, 'organizer_callback'], // Callback
            'cws-ninja-forms-config', // Page
            'general_data_section' // Section
        );

        // Mapping Data Section
        add_settings_section(
            'mapping_data_section', // ID
            'Mapping Data', // Title
            [$this, 'print_mapping_data_info'], // Callback
            'cws-ninja-forms-config' // Page
        );

        add_settings_field(
            'title', // ID
            'Event Title Field', // Title
            [$this, 'title_callback'], // Callback
            'cws-ninja-forms-config', // Page
            'mapping_data_section' // Section
        );

        add_settings_field(
            'date_time',
            'Start Date and Time Field',
            [$this, 'date_time_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );

        add_settings_field(
            'date_time_end',
            'End Date and Time Field',
            [$this, 'date_time_end_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );

        add_settings_field(
            'description',
            'Event Description Field',
            [$this, 'description_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );

        add_settings_field(
            'venue',
            'Venue Field',
            [$this, 'venue_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );
        add_settings_field(
            'cost',
            'Cost Field',
            [$this, 'cost_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );

        add_settings_field(
            'about',
            'About Field',
            [$this, 'about_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );

        add_settings_field(
            'ticket_url',
            'Ticket URL Field',
            [$this, 'ticket_url_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );
        add_settings_field(
            'booking_url',
            'Booking URL Field',
            [$this, 'booking_url_callback'],
            'cws-ninja-forms-config',
            'mapping_data_section'
        );
        // Mapping Custom Fields Section
        add_settings_section(
            'custom_fields_data_section', // ID
            'Custom Field Names', // Title
            [$this, 'custom_field_names_section'], // Callback
            'cws-ninja-forms-config' // Page
        );
        add_settings_field(
            'custom_ticket_url',
            'Ticket URL',
            [$this, 'custom_ticket_url_callback'],
            'cws-ninja-forms-config',
            'custom_fields_data_section'
        );
        add_settings_field(
            'custom_booking_url',
            'Booking URL',
            [$this, 'custom_booking_url_callback'],
            'cws-ninja-forms-config',
            'custom_fields_data_section'
        );
        add_settings_field(
            'custom_about_event',
            'About Event',
            [$this, 'custom_about_event_callback'],
            'cws-ninja-forms-config',
            'custom_fields_data_section'
        );

        // Add additional fields to the Mapping Data section as needed
    }

    public function sanitize($input) {
        $new_input = array();
        if(isset($input['form_id']))
            $new_input['form_id'] = absint($input['form_id']);

        if(isset($input['organizer']))
            $new_input['organizer'] = sanitize_text_field($input['organizer']);

        if(isset($input['title']))
            $new_input['title'] = sanitize_text_field($input['title']);

        if(isset($input['date_time']))
            $new_input['date_time'] = sanitize_text_field($input['date_time']);

        if(isset($input['date_time_end']))
            $new_input['date_time_end'] = sanitize_text_field($input['date_time_end']);

        if(isset($input['description']))
            $new_input['description'] = sanitize_text_field($input['description']);

        if(isset($input['venue']))
            $new_input['venue'] = sanitize_text_field($input['venue']);

        if(isset($input['cost']))
            $new_input['cost'] = sanitize_text_field($input['cost']);

        if(isset($input['about']))
            $new_input['about'] = sanitize_text_field($input['about']);

        if(isset($input['ticket_url']))
            $new_input['ticket_url'] = sanitize_text_field($input['ticket_url']);

        if(isset($input['booking_url']))
            $new_input['booking_url'] = sanitize_text_field($input['booking_url']);

        if(isset($input['custom_booking_url']))
            $new_input['custom_booking_url'] = sanitize_text_field($input['custom_booking_url']);

        if(isset($input['custom_about_event']))
            $new_input['custom_about_event'] = sanitize_text_field($input['custom_about_event']);

        if(isset($input['custom_ticket_url']))
            $new_input['custom_ticket_url'] = sanitize_text_field($input['custom_ticket_url']);

        return $new_input;
    }

    public function print_general_data_info() {
        print 'Enter the general configuration settings below:<br>';
        print 'Please add the element classes for controlling the date selection: Start Date with the class <strong>event_date_start</strong> and End Date with the class <strong>event_date_end</strong>.<br>';
    }

    public function print_mapping_data_info() {
        print 'Enter the mapping data for the event fields below:';
    }

    public function custom_field_names_section() {
        print 'Add custom field names for data mapping (*ACF Form):';
    }

    public function form_id_callback() {
        printf(
            '<input type="text" id="form_id" name="cws_ninja_forms_config[form_id]" value="%s" />',
            isset($this->options['form_id']) ? esc_attr($this->options['form_id']) : ''
        );
    }

    public function organizer_callback() {
        printf(
            '<input type="text" id="organizer" name="cws_ninja_forms_config[organizer]" value="%s" />',
            isset($this->options['organizer']) ? esc_attr($this->options['organizer']) : ''
        );
    }

    public function title_callback() {
        printf(
            '<input type="text" id="title" name="cws_ninja_forms_config[title]" value="%s" />',
            isset($this->options['title']) ? esc_attr($this->options['title']) : ''
        );
    }

    public function date_time_callback() {
        printf(
            '<input type="text" id="date_time" name="cws_ninja_forms_config[date_time]" value="%s" />',
            isset($this->options['date_time']) ? esc_attr($this->options['date_time']) : ''
        );
    }
    public function date_time_end_callback() {
        printf(
            '<input type="text" id="date_time_end" name="cws_ninja_forms_config[date_time_end]" value="%s" />',
            isset($this->options['date_time_end']) ? esc_attr($this->options['date_time_end']) : ''
        );
    }

    public function description_callback() {
        printf(
            '<input type="text" id="description" name="cws_ninja_forms_config[description]" value="%s" />',
            isset($this->options['description']) ? esc_attr($this->options['description']) : ''
        );
    }

    public function venue_callback() {
        printf(
            '<input type="text" id="venue" name="cws_ninja_forms_config[venue]" value="%s" />',
            isset($this->options['venue']) ? esc_attr($this->options['venue']) : ''
        );
    }

    public function cost_callback() {
        printf(
            '<input type="text" id="cost" name="cws_ninja_forms_config[cost]" value="%s" />',
            isset($this->options['cost']) ? esc_attr($this->options['cost']) : ''
        );
    }

    public function about_callback() {
        printf(
            '<input type="text" id="about" name="cws_ninja_forms_config[about]" value="%s" />',
            isset($this->options['about']) ? esc_attr($this->options['about']) : ''
        );
    }

    public function ticket_url_callback() {
        printf(
            '<input type="text" id="ticket_url" name="cws_ninja_forms_config[ticket_url]" value="%s" />',
            isset($this->options['ticket_url']) ? esc_attr($this->options['ticket_url']) : ''
        );
    }

    public function booking_url_callback() {
        printf(
            '<input type="text" id="booking_url" name="cws_ninja_forms_config[booking_url]" value="%s" />',
            isset($this->options['booking_url']) ? esc_attr($this->options['booking_url']) : ''
        );
    }
    public function custom_ticket_url_callback() {
        printf(
            '<input type="text" id="custom_ticket_url" name="cws_ninja_forms_config[custom_ticket_url]" value="%s" />',
            isset($this->options['custom_ticket_url']) ? esc_attr($this->options['custom_ticket_url']) : ''
        );
    }
    public function custom_booking_url_callback() {
        printf(
            '<input type="text" id="custom_booking_url" name="cws_ninja_forms_config[custom_booking_url]" value="%s" />',
            isset($this->options['custom_booking_url']) ? esc_attr($this->options['custom_booking_url']) : ''
        );
    }
    public function custom_about_event_callback() {
        printf(
            '<input type="text" id="custom_about_event" name="cws_ninja_forms_config[custom_about_event]" value="%s" />',
            isset($this->options['custom_about_event']) ? esc_attr($this->options['custom_about_event']) : ''
        );
    }

    // Add additional callbacks for other configurable fields as needed

}
// Initialize the settings class
if (is_admin()) {
    new Cws_Events_Automation_Admin_Settings();
}

