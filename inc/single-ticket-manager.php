<?php

if(! defined("ABSPATH") ) exit();

class SingleTicketsHandler extends CRMTicketManager {
    private static $instance;
    private $current_ticket;

    public static function instance() {
        
        if( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {

        // ...

        $this->init_hooks();
    }

    public function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_single_assets'));
        add_action('template_redirect', array($this, 'render_single_ticket'));
        add_action('wp_ajax_rate_ticket', array($this, 'set_ticket_rate'));
        add_action('wp_ajax_new_message', array($this, 'handle_user_message'));
        add_action('wp_ajax_download_file', array($this, 'fetch_annotation_file'));
        add_action('wp_ajax_ctm_ticket_read', array($this, 'set_ticket_read'));
        add_action('wp_ajax_check_high_importance_tickets', array($this, 'check_high_importance_tickets'));
        // add_action('wp_ajax_nopriv_rate_ticket', array($this, 'set_ticket_rate'));
        // add_action('wp_ajax_nopriv_new_message', array($this, 'handle_user_message'));
        // add_action('wp_ajax_nopriv_download_file', array($this, 'fetch_annotation_file'));
        // add_action('wp_ajax_nopriv_ticket_read', array($this, 'set_ticket_read'));
    }

    public function enqueue_single_assets() {
        
        if( get_query_var('new_ticket') !== '1' AND !get_query_var('ticket_id') ) return;

        $style_path = CTM_ASSETS_PATH . 'styles/single.css';
        $script_path = CTM_ASSETS_PATH . 'scripts/single.js';

        $single_data = array(
            'ajax_url'=> admin_url('admin-ajax.php'),
            'file_download_nonce'=> wp_create_nonce('file_download_nonce'),
            'read_ticket_nonce'=> wp_create_nonce('read_ticket_nonce'),
            'assets_url'=> CTM_ASSETS_URI
        );

        wp_enqueue_style('single-tickets-styles', CTM_ASSETS_URI . 'styles/single.css', NULL, filemtime($style_path));
        wp_enqueue_script('single-tickets-scripts', CTM_ASSETS_URI . 'scripts/single.js', array('jquery'), filemtime($style_path), true);
        wp_localize_script('single-tickets-scripts', 'single_data', $single_data);
    }

    public function render_single_ticket() {  

        parent::disallow_nopriv_users();
        
        if( get_query_var('new_ticket') !== '1' AND !get_query_var('ticket_id') ) return;
        
        $account_id = $this->get_user_account();
        if(!$account_id) die('لطفا ابتدا احراز هویت کنید');

        if(get_query_var('my_tickets') == '1' AND get_query_var('new_ticket') == '1') {
            $this->render_new_ticket($account_id);
            return;
        }

        define('CTM_PAGE', 'SINGLE');
        $ticket_id = get_query_var('ticket_id');

        $url = parent::$api_link . '/incidents(' . $ticket_id . ')';
        $fetched_ticket = parent::fetch_datas($url);
        if(!$fetched_ticket) {
            $archive_page = site_url('/my-tickets/?selected_account=') . $account_id;
            die("<h1 style='text-align: center; margin-top: 200px;'>تیکت مورد نظر یافت نشد</h1><a href='$archive_page' style='justify-content: center; display: flex;'>بازگشت به آرشیو تیکت ها</a>");
        }

        $url = parent::$api_link . '/systemusers?$select=fullname&$filter=systemuserid eq ' . $fetched_ticket->_ownerid_value;
        $owner = parent::fetch_datas($url);

        $url = parent::$api_link . '/annotations?$filter=_objectid_value eq ' . $ticket_id;
        $fetched_annotations = parent::fetch_datas($url);

        $this->create_current_ticket($fetched_ticket, $owner, $fetched_annotations);
        
        parent::set_date_and_time($this->current_ticket);
        parent::set_ticket_status($this->current_ticket);
        parent::set_ticket_modified_status($this->current_ticket);
        parent::set_ticket_importance_status($this->current_ticket);

        foreach($this->current_ticket['dialog'] as &$dialog) {
            parent::set_date_and_time($dialog);
        }

        unset($dialog);
        gc_collect_cycles();

        require_once CTM_TEMPLATES_PATH . 'single-ticket-template.php';
    }

    public function set_ticket_rate () {

        if(!wp_verify_nonce($_GET['rate_nonce'], 'ticket_rating_nonce')) wp_send_json_error(array('message'=> 'nonce verification failed'), 403);

        $account_id = $this->get_user_account();
        if(!$account_id) wp_send_json_error(array('message'=> 'accoutn verification failed'), 403);

        $ticket_rate = sanitize_text_field($_GET['rating']);
        if(!isset($ticket_rate) OR empty($ticket_rate)) wp_send_json_error(array('message'=> 'rate not confirmed'));

        $ticket_id = sanitize_text_field($_GET['ticket-id']);
        if(!isset($ticket_rate) OR empty($ticket_rate)) wp_send_json_error(array('message'=> 'ticket not found'), 404);

        $url = parent::$api_link . '/incidents(' . $ticket_id . ')';
        $body = json_encode(array('new_ticketrate'=> $ticket_rate));
        $method = 'PATCH';

        $response = parent::fetch_datas($url, $method, $body, true);
        $response_code = $response['response']['code'];

        if($response_code === 204) {
            wp_send_json_success(array('message'=> 'success'));
        }

        else wp_send_json_error(array('message'=> 'failed to set ticket rate'));
    }

    public function handle_user_message() {

        if(!wp_verify_nonce($_POST['submit_nonce'], 'new_message_nonce')) wp_send_json_error(array('message'=>'authentication failed'), 403);

        $account_id = $this->get_user_account();
        if(!$account_id) wp_send_json_error(array('message'=> 'account authentication failed'), 403);

        $user_file = $this->check_user_files();
        if(isset($user_file['name']) AND !empty($user_file['name'])) list($file_name, $file_type, $base64_content) = $this->prepare_file($user_file);
        
        $description = sanitize_text_field($_POST['ticket-reply-content']);
        if(!$description) wp_send_json_error(array('message'=> 'description required'));

        $ticket_id = sanitize_text_field($_POST['ticket-id']);
        
        if(!$ticket_id) {
            
            $title = sanitize_text_field($_POST['ticket-reply-title']);
            if(!$title) wp_send_json_error(array('message'=> 'title required'));

            $priority_code = sanitize_text_field($_POST['ticket-importance']);
            if(!$priority_code) wp_send_json_error(array('message'=> 'priority code required'));

            $high_priority_tickets = $this->get_user_high_priority_tickets($account_id);
            if($high_priority_tickets >= 3 AND $priority_code === 1) wp_send_json_error(array('message'=> 'don\'t try to manipulate site'));

            list($incident_response_code, $incident_response_body) = $this->create_new_incident($account_id, $title, $description, $priority_code);
            if($incident_response_code != 201) wp_send_json_error(array('message'=> 'failed to create ticket'));

            if(isset($base64_content) AND !empty($base64_content)) {
                list($annotation_response_code, $annotation_response_body) = $this->create_annotation($incident_response_body['incidentid'], "ATTACH", null, $file_name, $file_type, $base64_content);
                if($annotation_response_code != 201) wp_send_json_error(array('message'=> $annotation_response_body));
            }

            delete_transient("createdon_sorted_tickets_$account_id");
            delete_transient("modifiedon_sorted_tickets_$account_id");
            $incidentid = $incident_response_body['incidentid'];
            wp_send_json_success(array('message'=> 'success', 'link'=> site_url("/my-tickets/ticket-$incidentid")));
        }
        
        elseif(isset($ticket_id) AND !empty($ticket_id)) {
            
            $prev_tickets = get_transient("createdon_sorted_tickets_$account_id");
            $ticket_index = array_search($ticket_id, array_column($prev_tickets, 'id'));
            if(in_array($prev_tickets[$ticket_index]['status_code'], ['5', '6', '1000', '2000'])) wp_send_json_success(array('message'=> 'don\'t try to manipulation'), 403);

            list($annotation_response_code, $annotation_response_body) = $this->create_annotation($ticket_id, null, $description, $file_name, $file_type, $base64_content);
            if($annotation_response_code != 201) wp_send_json_error(array('message'=> $annotation_response_body));
            
            $status = sanitize_text_field($_POST['status']);
            if($status === 'closed') {

                $url = parent::$api_link . '/CloseIncident';
                $method = 'POST';

                $body = json_encode(array(
                    "IncidentResolution"=> array(
                        "@odata.type"=> "Microsoft.Dynamics.CRM.incidentresolution",
                        "subject"=> "Ticket resolved from wordpress",
                        "incidentid@odata.bind"=> "/incidents($ticket_id)"
                    ),
                    "Status"=> 5
                ));

                $status_response = parent::fetch_datas($url, $method, $body, true);
                $status_code = $status_response['response']['code'];

                if($status_code != 204) wp_send_json_error(array('message'=> $status_response));
                
                $this->update_local_tickets($account_id, $ticket_id, ['status'=> 'resolved', 'status_code'=> '5']);

            }

            $customer_name = $this->get_customer_name($account_id);

            $annotation_response_body = json_decode(json_encode($annotation_response_body), true);

            $annotation = array(
                'annotationid'=> $annotation_response_body['annotationid'],
                'notetext'=> $annotation_response_body['notetext'],
                'filename'=> $annotation_response_body['filename'],
                'owner_name'=> $customer_name,
                'created'=> $annotation_response_body['createdon'],
                'user_image'=> esc_url(get_avatar_url(get_current_user_id())),
            );

            parent::set_date_and_time($annotation);

            wp_send_json_success(array('annotation'=> $annotation));
        }
    }

    public function fetch_annotation_file() {
        
        if(!wp_verify_nonce($_GET['nonce'], 'file_download_nonce')) {
            wp_send_json_error(array('message'=> 'nonce verification failed'));
        }

        $data = sanitize_text_field($_GET['data']);
        $url = parent::$api_link . '/annotations(' . $data . ')?$select=filename,mimetype,documentbody';
        $file = parent::fetch_datas($url);
        $file_content = base64_decode($file->documentbody);

        header('Content-Type: ' . 'image/png');
        header('Content-Disposition: attachment; filename="' . $file->filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($file_content));
        
        exit($file_content);
    }

    public function set_ticket_read() {

        if(!wp_verify_nonce($_GET['nonce'], 'read_ticket_nonce')) wp_send_json_error(array('message'=> 'nonce verification failed'), 403);

        $account_id = $this->get_user_account();
        if(!$account_id) wp_send_json_error(array('message'=> 'account verification failed'), 403);

        $ticket_id = sanitize_text_field($_GET['ticket_id']);
        if(!$ticket_id) wp_send_json_error(array('message'=> 'ticket not found'), 404);
        
        $url = parent::$api_link . '/incidents(' . $ticket_id . ')';
        $body = json_encode(array('new_isread'=> true));
        $method = 'PATCH';

        $response = parent::fetch_datas($url, $method, $body, true);
        $response_code = $response['response']['code'];

        if($response_code === 204) {
            $this->update_local_tickets($account_id, $ticket_id, ['new_isread'=> 'true']);
            wp_send_json_success(array('message'=> 'ticket read successfully'));
        } 

        else wp_send_json_error(array('message'=> 'failed to read ticket'));
        
    }

    public function check_high_importance_tickets() {
        
        if(!wp_verify_nonce($_GET['importance_check_nonce'], 'new_message_nonce')) wp_send_json_error(array('message'=> 'nonce authentication failed'), 403);

        $account_id = sanitize_text_field($_GET['account_id']);
        if(!$account_id) wp_send_json_success(array('message'=> 'account not found'), 404);



        wp_send_json_success(array('message'=> 'success'));
    }

    private function render_new_ticket($account_id) {
        define("CTM_PAGE", 'NEW_TICKET');
            
        $high_priority_tickets_count = $this->get_user_high_priority_tickets($account_id);
        
        include_once CTM_TEMPLATES_PATH . 'sections/new-ticket.php';
        return;
    }

    private function update_local_tickets($account_id, $ticket_id, $args) {

        $prev_tickets = get_transient("createdon_sorted_tickets_$account_id");
        
        $ticket_index = array_search($ticket_id, array_column($prev_tickets, 'id'));
        
        foreach($args as $prop=>$value) {
            $prev_tickets[$ticket_index][$prop] = $value;
        }

        set_transient("createdon_sorted_tickets_$account_id", $prev_tickets);
    }

    private function check_user_files() {
        if( (isset($_FILES['audio-input']['name']) AND !empty($_FILES['audio-input']['name'])) AND (isset($_FILES['file-input']['name']) AND !empty($_FILES['file-input']['name'])) ) {
            wp_send_json_error(array('message'=> 'don\'t try to manipulation :)'), 403);
        }
        
        if( (isset($_FILES['audio-input']) AND !empty($_FILES['audio-input']) AND $_FILES['audio-input']['error'] == UPLOAD_ERR_OK) ) {
            return $_FILES['audio-input'];
        }

        else if((isset($_FILES['file-input']) AND !empty($_FILES['file-input']) AND $_FILES['file-input']['error'][0] == UPLOAD_ERR_OK)) {
            return $_FILES['file-input'];
        }

        else if ($_FILES['file-input']['error'] != UPLOAD_ERR_OK OR $_FILES['audio-input']['error'] != UPLOAD_ERR_OK) {
            wp_send_json_error(array('message'=> 'file upload failed'), 500);
        
        }
    }

    private function prepare_file($file) {
            $file_name = sanitize_file_name($file['name']);
            $tmp_name = sanitize_text_field($file['tmp_name']);
            $file_size = sanitize_text_field($file['size']);

            $file_type = wp_check_filetype($file_name);

            $allowed_mimes = array(
                'audio/mpeg'=> 'mp3',
                'audio/wav'=> 'wav',
                'audio/ogg'=> 'ogg',
                'audio/webm'=> 'webm',
                'video/webm'=> 'webm', // for misrecognization of finfo_file in webm container
                'image/png'=> 'png', 
                'image/jpg'=> 'jpg', 
                'image/jpeg'=> 'jpeg',
                'application/zip' => 'zip', 
                'application/x-compressed'=> 'zip', 
                'application/x-zip-compressed'=> 'rar', 
                'application/vnd.rar'=> 'rar',
                'application/x-rar'=> 'rar'
            );

            if(!in_array($file_type['ext'], $allowed_mimes)) {
                wp_send_json_error(array('message'=> 'unallowed file type'), 403);
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            if(!array_key_exists($mime, $allowed_mimes)) {
                wp_send_json_error(array('message'=> $mime), 403);
            }

            $max_size = 5 * 1024 * 1024;
            if($file_size > $max_size) {
                wp_send_json_error(array('message'=> 'large file'), 403);
            }

            $uploaded_content = file_get_contents($tmp_name);
            return array($file_name, $file_type, base64_encode($uploaded_content));
    }

    private function create_new_incident($account_id, $title, $description, $code) {
        $url = parent::$api_link . '/incidents';
        $method = 'POST';
        
        $body = json_encode(array(
            'customerid_account@odata.bind'=> '/accounts(' . $account_id .')',
            'title'=> $title,
            'description'=> $description,
            'prioritycode'=> $code,
            'caseorigincode'=> 100000000,
            'new_isread'=> true
        ));

        $response = parent::fetch_datas($url, $method, $body, true, 'return=representation');
        $response_code = $response['response']['code'];
        $response_body = $response['body'];

        return array($response_code, json_decode($response_body, true));
    }

    private function create_annotation($id, $subject = null, $note_text = null, $name = null, $type = null, $file = null) {

        $url = parent::$api_link . '/annotations';
        $method = 'POST';

        $body = array(
            'objectid_incident@odata.bind'=> "/incidents($id)",
            'notetext'=> $note_text,
            'subject'=> $subject,
        );

        if($file) {
            $body['filename'] = $name;
            $body['mimetype'] = $type['mime'];
            $body['documentbody'] = $file;
        }

        $body = json_encode($body);

        $response = parent::fetch_datas($url, $method, $body, true, 'return=representation');

        return array($response['response']['code'], json_decode($response['body']));
    }

    private function get_customer_name($account_id) {
        $customer_name = parent::fetch_datas("https://crm3pcoorg.ever247.net/api/data/v9.0/accounts?\$filter=accountid eq $account_id&\$select=name");
        return $customer_name->value[0]->name;
    }

    private function create_current_ticket($ticket, $owner, $annotations = null) {

        $this->current_ticket = array(
            'id'=> $ticket->incidentid,
            'ticket_id'=> $ticket->ticketnumber,
            'importance'=> $ticket->prioritycode ? 4 - intval($ticket->prioritycode) : 1,
            'title'=> $ticket->title,
            'created'=> $ticket->createdon,
            'modified'=> $ticket->modifiedon,
            'status_code'=> $ticket->statuscode ?? 1,
            'support'=> $owner->value[0]->fullname ?? 'تعریف نشده',
            'support_id'=> !$ticket->new_supportid ? '1' : $ticket->new_supportid,
            'ticket_rate'=> $ticket->new_ticketrate,
            'user'=> 'user',
            'dialog'=> array()
        );

        $ticket_is_read = json_decode(json_encode($ticket), true)['new_isread'];
        if($ticket_is_read == true OR $ticket_is_read === null) $this->current_ticket['is_read'] = 'true';
        elseif($ticket_is_read == false) $this->current_ticket['is_read'] =  'false';

        $customer_name = $this->get_customer_name($ticket->_customerid_value);

        if(!empty($ticket->description)) {

            $this->current_ticket['dialog'][] = array(
                'annotation_id'=> $ticket->incidentid,
                'content' => $ticket->description,
                'created' => $ticket->createdon,
                'class_name' => 'user-image',
                'owner'=> 'user',
            );

            end($this->current_ticket['dialog']);
            $current_dialog = &$this->current_ticket['dialog'][key($this->current_ticket['dialog'])];

            $current_dialog['owner_name'] = $customer_name;
            parent::set_date_and_time($current_dialog);
            $current_dialog['subject'] = $customer_name . ' - ' . $current_dialog['jalali_created'];
        }

        if(!$annotations OR count($annotations->value) === 0) return;

        foreach($annotations->value as $dialog) {

            if($dialog->subject === "ATTACH" AND !empty($dialog->filename)) {
                end($this->current_ticket['dialog']);
                $this->current_ticket['dialog'][key($this->current_ticket['dialog'])]['annotation_id'] = $dialog->annotationid;
                $this->current_ticket['dialog'][key($this->current_ticket['dialog'])]['filename'] = $dialog->filename;
                continue;
            }
        
            if(empty($dialog->notetext) AND empty($dialog->filename)) continue;

            list($owner_name, $date_time) = explode('-', trim($dialog->subject));
            list($jalali_date, $jalali_time) = explode(' ', trim($date_time));

            $this->current_ticket['dialog'][] = array(
                'annotation_id'=> $dialog->annotationid,
                'subject'=> $dialog->subject,
                'owner_name'=> trim($owner_name) === 'عطا اشرافی' ? $customer_name : $owner_name,
                'content' => $dialog->notetext,
                'created' => $dialog->createdon,
                'jalali_created' => $jalali_date,
                'jalali_time' => $jalali_time,
                'class_name'=> trim($owner_name) === 'عطا اشرافی' ? 'user-image' : 'support-img support-' . $this->current_ticket['support_id'],
                'owner'=> trim($owner_name) === 'عطا اشرافی' ? 'user' : 'admin',
                // 'class_name'=> $this->current_ticket['dialog'][key($this->current_ticket['dialog'])]['owner'] === 'user' ? 'user-image' : "support-img support-" . $this->current_ticket['support_id'],
            );
        
            if(!empty($dialog->filename) AND !empty($dialog->filesize)) {
                end($this->current_ticket['dialog']);
                $this->current_ticket['dialog'][key($this->current_ticket['dialog'])]['filename'] = $dialog->filename;
            }
        }
    }

    private function get_user_account() {
        $user_id = get_current_user_id();
        $account_id = get_user_meta($user_id, 'account_id', true);
        return $account_id;
    }

    private function get_user_high_priority_tickets($account_id) {
        $last_month = date('Y-m-d', strtotime('last month'));
        $url = parent::$api_link . '/incidents?$filter=createdon gt ' . $last_month . ' and prioritycode eq 1 and _customerid_value eq ' . $account_id . ' and statecode eq 0';

        $high_priority_tickets = parent::fetch_datas($url);
        return count($high_priority_tickets->value);
    }
}