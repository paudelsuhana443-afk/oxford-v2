<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DYNATAFO_Contact_URL_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    public function get_name() { return 'dynatafo-contact-url'; }
    public function get_title() { return esc_html__( 'Contact URL (DTE)', 'dynamic-tags-for-elementor' ); }
    public function get_group() { return 'dynatafo-actions-group'; }
    public function get_categories() { return [ 'url' ]; }

    protected function register_controls() {
        $this->add_control(
            'link_type',
            [
                'label'   => esc_html__( 'Type', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'email',
                'options' => [
                    'email'     => esc_html__( 'Email', 'dynamic-tags-for-elementor' ),
                    'tel'       => esc_html__( 'Tel', 'dynamic-tags-for-elementor' ),
                    'sms'       => esc_html__( 'SMS', 'dynamic-tags-for-elementor' ),
                    'whatsapp'  => esc_html__( 'WhatsApp', 'dynamic-tags-for-elementor' ),
                    'messenger' => esc_html__( 'Messenger', 'dynamic-tags-for-elementor' ),
                    'viber'     => esc_html__( 'Viber', 'dynamic-tags-for-elementor' ),
                    'waze'      => esc_html__( 'Waze', 'dynamic-tags-for-elementor' ),
                    'google_calendar'  => esc_html__( 'Google Calendar', 'dynamic-tags-for-elementor' ),
                    'outlook_calendar' => esc_html__( 'Outlook Calendar', 'dynamic-tags-for-elementor' ),
                    'yahoo_calendar'   => esc_html__( 'Yahoo Calendar', 'dynamic-tags-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'mail_to',
            [
                'label'       => esc_html__( 'Email', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => get_option( 'admin_email' ),
                'condition'   => [
                    'link_type' => 'email',
                ],
            ]
        );

        $this->add_control(
            'mail_subject',
            [
                'label'       => esc_html__( 'Subject', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => [
                    'link_type' => 'email',
                ],
            ]
        );

        $this->add_control(
            'mail_body',
            [
                'label'     => esc_html__( 'Message', 'dynamic-tags-for-elementor' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'condition' => [
                    'link_type' => 'email',
                ],
            ]
        );

        $this->add_control(
            'tel_number',
            [
                'label'       => esc_html__( 'Number', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => '+12025550148',
                'condition'   => [
                    'link_type' => [ 'tel', 'sms', 'whatsapp', 'viber' ],
                ],
            ]
        );

        $this->add_control(
            'username',
            [
                'label'       => esc_html__( 'Username', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => [
                    'link_type' => [ 'messenger' ],
                ],
            ]
        );

        $this->add_control(
            'viber_action',
            [
                'label'   => esc_html__( 'Action', 'dynamic-tags-for-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'contact',
                'options' => [
                    'contact' => esc_html__( 'Contact', 'dynamic-tags-for-elementor' ),
                    'add'     => esc_html__( 'Add', 'dynamic-tags-for-elementor' ),
                ],
                'condition' => [
                    'link_type' => 'viber',
                ],
            ]
        );

        $this->add_control(
            'waze_address',
            [
                'label'       => esc_html__( 'Location', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => [
                    'link_type' => 'waze',
                ],
            ]
        );

        $calendar_condition = [
            'link_type' => [ 'google_calendar', 'outlook_calendar', 'yahoo_calendar' ],
        ];

        $this->add_control(
            'event_title',
            [
                'label'       => esc_html__( 'Title', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => $calendar_condition,
            ]
        );

        $this->add_control(
            'event_description',
            [
                'label'     => esc_html__( 'Description', 'dynamic-tags-for-elementor' ),
                'type'      => \Elementor\Controls_Manager::TEXTAREA,
                'condition' => $calendar_condition,
            ]
        );

        $this->add_control(
            'event_location',
            [
                'label'       => esc_html__( 'Location', 'dynamic-tags-for-elementor' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'condition'   => $calendar_condition,
            ]
        );

        $this->add_control(
            'event_start_date',
            [
                'label'     => esc_html__( 'Start', 'dynamic-tags-for-elementor' ),
                'type'      => \Elementor\Controls_Manager::DATE_TIME,
                'condition' => $calendar_condition,
            ]
        );

        $this->add_control(
            'event_end_date',
            [
                'label'     => esc_html__( 'End', 'dynamic-tags-for-elementor' ),
                'type'      => \Elementor\Controls_Manager::DATE_TIME,
                'condition' => $calendar_condition,
            ]
        );
    }

    private function date_to_iso( $date, $all_day = false ) {
        $time = strtotime( (string) $date );

        if ( false === $time ) {
            return '';
        }

        return $all_day ? gmdate( 'Ymd', $time ) : gmdate( 'Ymd\\THis', $time );
    }

    private function date_to_ics( $date ) {
        $time = strtotime( (string) $date );

        if ( false === $time ) {
            return '';
        }

        return gmdate( 'Y-m-d\\TH:i:s', $time );
    }

    private function build_google_calendar_url( array $settings ) {
        $title = (string) ( $settings['event_title'] ?? '' );
        $desc  = (string) ( $settings['event_description'] ?? '' );
        $loc   = (string) ( $settings['event_location'] ?? '' );

        $start = $this->date_to_iso( $settings['event_start_date'] ?? '' );
        $end   = $this->date_to_iso( $settings['event_end_date'] ?? '' );

        $args = [
            'action'   => 'TEMPLATE',
            'text'     => $title,
            'details'  => $desc,
            'location' => $loc,
        ];

        if ( '' !== $start || '' !== $end ) {
            $args['dates'] = $start . '/' . $end;
        }

        return esc_url_raw( add_query_arg( $args, 'https://www.google.com/calendar/render' ) );
    }

    private function build_outlook_calendar_url( array $settings ) {
        $args = [
            'path'    => '/calendar/action/compose',
            'rru'     => 'addevent',
            'subject' => (string) ( $settings['event_title'] ?? '' ),
            'body'    => (string) ( $settings['event_description'] ?? '' ),
            'location' => (string) ( $settings['event_location'] ?? '' ),
            'startdt' => $this->date_to_ics( $settings['event_start_date'] ?? '' ),
            'enddt'   => $this->date_to_ics( $settings['event_end_date'] ?? '' ),
        ];

        return esc_url_raw( add_query_arg( $args, 'https://outlook.live.com/owa/' ) );
    }

    private function build_yahoo_calendar_url( array $settings ) {
        $start = $this->date_to_iso( $settings['event_start_date'] ?? '' );
        $end   = $this->date_to_iso( $settings['event_end_date'] ?? '' );
        $dur   = '0100';

        if ( '' !== $start && '' !== $end ) {
            $diff = strtotime( (string) ( $settings['event_end_date'] ?? '' ) ) - strtotime( (string) ( $settings['event_start_date'] ?? '' ) );

            if ( $diff > 0 ) {
                $hours = floor( $diff / 3600 );
                $mins  = floor( ( $diff % 3600 ) / 60 );
                $dur   = sprintf( '%02d%02d', $hours, $mins );
            }
        }

        $args = [
            'v'      => 60,
            'view'   => 'd',
            'type'   => 20,
            'title'  => (string) ( $settings['event_title'] ?? '' ),
            'st'     => $start,
            'dur'    => $dur,
            'desc'   => (string) ( $settings['event_description'] ?? '' ),
            'in_loc' => (string) ( $settings['event_location'] ?? '' ),
        ];

        return esc_url_raw( add_query_arg( $args, 'https://calendar.yahoo.com/' ) );
    }

    public function get_value( array $options = [] ) {
        $settings  = $this->get_settings();
        $link_type = isset( $settings['link_type'] ) ? (string) $settings['link_type'] : 'email';

        if ( 'email' === $link_type ) {
            $email = sanitize_email( (string) ( $settings['mail_to'] ?? '' ) );

            if ( '' === $email ) {
                return '';
            }

            $query_args = [];

            if ( ! empty( $settings['mail_subject'] ) ) {
                $query_args['subject'] = rawurlencode( (string) $settings['mail_subject'] );
            }

            if ( ! empty( $settings['mail_body'] ) ) {
                $query_args['body'] = rawurlencode( (string) $settings['mail_body'] );
            }

            $link = 'mailto:' . $email;

            return ! empty( $query_args ) ? esc_url_raw( add_query_arg( $query_args, $link ) ) : esc_url_raw( $link );
        }

        $number = isset( $settings['tel_number'] ) ? (string) $settings['tel_number'] : '';
        $number = preg_replace( '/[^\d\+]/', '', $number );

        if ( in_array( $link_type, [ 'tel', 'sms', 'whatsapp', 'viber' ], true ) && '' === $number ) {
            return '';
        }

        switch ( $link_type ) {
            case 'tel':
                return esc_url_raw( 'tel:' . $number );
            case 'sms':
                return esc_url_raw( 'sms:' . $number );
            case 'whatsapp':
                return esc_url_raw( 'https://api.whatsapp.com/send?phone=' . rawurlencode( $number ) );
            case 'messenger':
                $username = trim( (string) ( $settings['username'] ?? '' ) );
                return '' !== $username ? esc_url_raw( 'https://m.me/' . rawurlencode( $username ) ) : '';
            case 'viber':
                $action = isset( $settings['viber_action'] ) ? sanitize_key( (string) $settings['viber_action'] ) : 'contact';
                return esc_url_raw( 'viber://chat?number=' . rawurlencode( $number ) . '&action=' . rawurlencode( $action ) );
            case 'waze':
                $location = trim( (string) ( $settings['waze_address'] ?? '' ) );
                if ( '' === $location ) {
                    return '';
                }
                return esc_url_raw( add_query_arg( [
                    'q'        => $location,
                    'z'        => 10,
                    'navigate' => 'yes',
                ], 'https://waze.com/ul' ) );
            case 'google_calendar':
                return $this->build_google_calendar_url( $settings );
            case 'outlook_calendar':
                return $this->build_outlook_calendar_url( $settings );
            case 'yahoo_calendar':
                return $this->build_yahoo_calendar_url( $settings );
            default:
                return '';
        }
    }
}