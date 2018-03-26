<?php 
/**
 * 邮件发送类
 * @author LuckPHP
 *
 */
namespace Luck\Tool;
class Email{ 
    private $smtp_server; //SMTP服务器    
    private $smtp_port; //SMTP服务器端口
    private $myEmailUser; //使用发送email账号
    private $myEmailPass; //使用发送email密码
    private $time_out = 30; //发送超时限制
    private $host_name = 'localhost'; 
    private $log_file = ''; 
    public $debug = FALSE; //是否开启调试 开启true 默认false关闭
    private $auth = TRUE; 
    private $sock = FALSE; 
    
    /**
     * 基础配置
     * @param $smtp_server SMTP服务器
     * @param $smtp_port SMTP服务器端口
     * @param $myEmailUser 使用发送email账号
     * @param $myEmailPass 使用发送email密码               
     * 
     */    
    public function __construct($smtp_server, $smtp_port = 25, $myEmailUser, $myEmailPass) { 
        $this->smtp_port = $smtp_port; 
        $this->smtp_server = $smtp_server; 
        $this->myEmailUser = $myEmailUser; 
        $this->myEmailPass = $myEmailPass; 
    } 
    
    /**
     * 执行发送
     * @param $toEmail 接收email
     * @param $fromEmail 发送email
     * @param $emailTitle 邮件标题
     * @param $emailContent 邮件内容    
     * @param $mailtype 邮件格式(HTML/TXT),TXT为文本邮件       
     * 
     */      
    public function sendmail($toEmail, $fromEmail, $emailTitle, $emailContent, $mailtype) { 
        $cc = '';
        $bcc = '';
        $additional_headers = '';
        $mail_from = $this->get_address($this->strip_comment($fromEmail)); 
        $emailContent = preg_replace("/(^|(\r\n))(\\.)/", "\\1.\\3", $emailContent); 
        $header = '';
        $header .= "MIME-Version:1.0\r\n"; 
        if($mailtype == "HTML"){ 
            $header .= "Content-Type:text/html\r\n"; 
        } 
            $header .= "To: ".$toEmail."\r\n"; 
        if($cc != "") { 
            $header .= "Cc: ".$cc."\r\n"; 
        } 
        $header .= "From: $fromEmail<".$fromEmail.">\r\n"; 
        $header .= "Subject: ".$emailTitle."\r\n"; 
        $header .= $additional_headers; 
        $header .= "Date: ".date("r")."\r\n"; 
        $header .= "X-Mailer:By Redhat (PHP/".phpversion().")\r\n"; 
        list($msec, $sec) = explode(" ", microtime()); 
        $header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$mail_from.">\r\n"; 
        $toEmail = explode(",", $this->strip_comment($toEmail)); 
        if($cc != "") { 
        $toEmail = array_merge($toEmail, explode(",", $this->strip_comment($cc))); 
        } 
        if($bcc != "") { 
        $toEmail = array_merge($toEmail, explode(",", $this->strip_comment($bcc))); 
        } 
        $sent = TRUE; 
        foreach ($toEmail as $rcpt_to) { 
            $rcpt_to = $this->get_address($rcpt_to); 
            if (!$this->smtp_sockopen($rcpt_to)) { 
                $this->log_write("Error: Cannot send email to ".$rcpt_to."\n"); 
                $sent = FALSE; 
                continue; 
            } 
            if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $emailContent)) { 
                $this->log_write("E-mail has been sent to <".$rcpt_to.">\n"); 
            } else { 
                $this->log_write("Error: Cannot send email to <".$rcpt_to.">\n"); 
                $sent = FALSE; 
            } 
            fclose($this->sock); 
            $this->log_write("Disconnected from remote host\n"); 
        } 
        return $sent; 
    } 
 
   
    function smtp_send($helo, $fromEmail, $toEmail, $header, $emailContent = "") { 
        if (!$this->smtp_putcmd("HELO", $helo)) { 
            return $this->smtp_error("sending HELO command"); 
        } 
        if($this->auth){ 
            if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->myEmailUser))) { 
                return $this->smtp_error("sending HELO command"); 
            } 
            if (!$this->smtp_putcmd("", base64_encode($this->myEmailPass))) { 
                return $this->smtp_error("sending HELO command"); 
            } 
        } 
        if (!$this->smtp_putcmd("MAIL", "FROM:<".$fromEmail.">")) { 
            return $this->smtp_error("sending MAIL FROM command"); 
        } 
         
        if (!$this->smtp_putcmd("RCPT", "TO:<".$toEmail.">")) { 
            return $this->smtp_error("sending RCPT TO command"); 
        } 
 
        if (!$this->smtp_putcmd("DATA")) { 
            return $this->smtp_error("sending DATA command"); 
        } 
         
        if (!$this->smtp_message($header, $emailContent)) { 
            return $this->smtp_error("sending message"); 
        } 
 
        if (!$this->smtp_eom()) { 
            return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]"); 
        } 
         
        if (!$this->smtp_putcmd("QUIT")) { 
            return $this->smtp_error("sending QUIT command"); 
        } 
        return TRUE; 
    } 
 
    function smtp_sockopen($address) { 
        if ($this->smtp_server == "") { 
            return $this->smtp_sockopen_mx($address); 
        } else { 
            return $this->smtp_sockopen_relay(); 
        } 
    } 
 
    function smtp_sockopen_relay() { 
        $this->log_write("Trying to ".$this->smtp_server.":".$this->smtp_port."\n"); 
        $this->sock = @fsockopen($this->smtp_server, $this->smtp_port, $errno, $errstr, $this->time_out); 
        if (!($this->sock && $this->smtp_ok())) { 
            $this->log_write("Error: Cannot connenct to relay host ".$this->smtp_server."\n"); 
            $this->log_write("Error: ".$errstr." (".$errno.")\n"); 
            return FALSE; 
        } 
        $this->log_write("Connected to relay host ".$this->smtp_server."\n"); 
        return TRUE;; 
    } 
 
    function smtp_sockopen_mx($address) { 
        $domain = preg_replace("/^.+@([^@]+)$/", "\\1", $address); 
        if (!@getmxrr($domain, $MXHOSTS)) { 
            $this->log_write("Error: Cannot resolve MX \"".$domain."\"\n"); 
            return FALSE; 
        } 
        foreach ($MXHOSTS as $host) { 
            $this->log_write("Trying to ".$host.":".$this->smtp_port."\n"); 
            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out); 
            if (!($this->sock && $this->smtp_ok())) { 
                $this->log_write("Warning: Cannot connect to mx host ".$host."\n"); 
                $this->log_write("Error: ".$errstr." (".$errno.")\n"); 
                continue; 
            } 
            $this->log_write("Connected to mx host ".$host."\n"); 
            return TRUE; 
        } 
        $this->log_write("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n"); 
        return FALSE; 
    } 
 
    function smtp_message($header, $emailContent) { 
        fputs($this->sock, $header."\r\n".$emailContent); 
        $this->smtp_debug("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$emailContent."\n> ")); 
         
        return TRUE; 
    } 
 
    function smtp_eom() { 
        fputs($this->sock, "\r\n.\r\n"); 
        $this->smtp_debug(". [EOM]\n"); 
         
        return $this->smtp_ok(); 
    } 
 
    function smtp_ok() { 
        $response = str_replace("\r\n", "", fgets($this->sock, 512)); 
        $this->smtp_debug($response."\n");     
        if (!preg_match("/^[23]/", $response)) { 
            fputs($this->sock, "QUIT\r\n"); 
            fgets($this->sock, 512); 
            $this->log_write("Error: Remote host returned \"".$response."\"\n"); 
            return FALSE; 
        } 
        return TRUE; 
    } 
 
    function smtp_putcmd($cmd, $arg = "") { 
        if ($arg != "") { 
            if($cmd=="") $cmd = $arg; 
            else $cmd = $cmd." ".$arg; 
        } 
        fputs($this->sock, $cmd."\r\n"); 
        $this->smtp_debug("> ".$cmd."\n"); 
        return $this->smtp_ok(); 
    } 
 
    function smtp_error($string) { 
        $this->log_write("Error: Error occurred while ".$string.".\n"); 
        return FALSE; 
    } 
 
    function log_write($message) { 
        $this->smtp_debug($message); 
         
        if ($this->log_file == "") { 
            return TRUE; 
        } 
 
        $message = date("M d H:i:s ").get_current_myEmailUser()."[".getmypid()."]: ".$message; 
        if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) { 
            $this->smtp_debug("Warning: Cannot open log file \"".$this->log_file."\"\n"); 
            return FALSE; 
        } 
        flock($fp, LOCK_EX); 
        fputs($fp, $message); 
        fclose($fp); 
         
        return TRUE; 
    } 
 
    function strip_comment($address) { 
        $comment = "/\\([^()]*\\)/"; 
        while (preg_match($comment, $address)) { 
            $address = preg_replace($comment, "", $address); 
        }   
        return $address; 
    } 
 
    function get_address($address) { 
        $address = preg_replace("/([ \t\r\n])+/", "", $address); 
        $address = preg_replace("/^.*<(.+)>.*$/", "\\1", $address); 
         
        return $address; 
    } 
 
    function smtp_debug($message) { 
        if ($this->debug) { 
            echo $message."<br>"; 
        } 
    } 
 
    function get_attach_type($image_tag) { 
        $filedata = array();  
        $img_file_con=fopen($image_tag,"r"); 
        unset($image_data); 
        while ($tem_buffer=AddSlashes(fread($img_file_con,filesize($image_tag)))) 
        $image_data.=$tem_buffer; 
        fclose($img_file_con); 
     
        $filedata['context'] = $image_data; 
        $filedata['filename']= basename($image_tag); 
        $extension=substr($image_tag,strrpos($image_tag,"."),strlen($image_tag)-strrpos($image_tag,".")); 
        switch($extension){ 
            case ".gif": 
                $filedata['type'] = "image/gif"; 
                break; 
            case ".gz": 
                $filedata['type'] = "application/x-gzip"; 
                break; 
            case ".htm": 
                $filedata['type'] = "text/html"; 
                break; 
            case ".html": 
                $filedata['type'] = "text/html"; 
                break; 
            case ".jpg": 
                $filedata['type'] = "image/jpeg"; 
                break; 
            case ".tar": 
                $filedata['type'] = "application/x-tar"; 
                break; 
            case ".txt": 
                $filedata['type'] = "text/plain"; 
                break; 
            case ".zip": 
                $filedata['type'] = "application/zip"; 
                break; 
            default: 
                $filedata['type'] = "application/octet-stream"; 
                break; 
        } 
        return $filedata; 
    } 

 
} 
?> 