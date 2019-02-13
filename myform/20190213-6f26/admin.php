<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "floydwick@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "90c394" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'85A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxmmMLQii4lMEWlgCGWY6oAkFtAq0sDo6BAQgKouhLUh0EEEyX1Lo6YuXboqMmsakvtEpjA0uiLUQc0DioWii4kA1QWg2cHaytoQgOIW1gBGoL0BKG4eqPCjIsTiPgBB3s1W582FYgAAAABJRU5ErkJggg==',
			'18FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA1qRxVgdWFtZGximOiCJiTqINLo2MAQEoOgFqWN0EEFy38qslWFLQ1dmTUNyH5o6qBjIPMbQEEwxNHWYekVDgG5GExuo8KMixOI+ABoyx/mi4+6zAAAAAElFTkSuQmCC',
			'F604' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQximMDQEIIkFNLC2MoQyNKKKiTQyOjq0ook1sDYETAlAcl9o1LSwpauioqKQ3BfQINrK2hDogG6ea0NgaAiamKOjAza3oIlhunmgwo+KEIv7AHa8zvJel172AAAAAElFTkSuQmCC',
			'9CE9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHaY6IImJTGFtdG1gCAhAEgtoFWlwbWB0EEETY0WIgZ00beq0VUtDV0WFIbmP1RWkjmEqsl4GsF6gXUhiAmA7GFDswOYWbG4eqPCjIsTiPgCV8Muqi/X7QQAAAABJRU5ErkJggg==',
			'42B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AQAhFuYINdB8srsfkaJwGCjfA28BCp/RKLlpqIr974YcX4LyNwp/yjZ+ngpKEIyu4ok0U91IZLOvcMXSwbFOm4Ffrue9yLEvwYwdHIx1CVwQYlTvWXAjbjZ6hti53fj5KFtjoD/97Lw9+F5M2y/ERYk+5AAAAAElFTkSuQmCC',
			'1192' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QsQ2AMAwE7cIbhH3MBo+UNNkApjAFG8AQZEoQlZVQghR/d9LrT6bSnFFP+cWPlUCJDnVMlMGjAo4NKhCbNFRdMVhwfudS8jnnkp3fsxGxar1r2GoXNuwNu108G6IkSpxiB//7MC9+F8CIxwqrIJaqAAAAAElFTkSuQmCC',
			'A720' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgDGBodHR2mOiCJiUxhaHRtCAgIQBILaAXpC3QQQXJf1NJV01atzMyahuQ+oLoAhlZGmDowDA1ldGCYgioW0MraAFSJZocI0I0MKG4BibGGBqC4eaDCj4oQi/sA5JLMCWkzIKIAAAAASUVORK5CYII=',
			'680F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIaGIImJTGFtZQhldEBWF9Ai0ujo6Igq1sDaytoQCBMDOykyamXY0lWRoVlI7guZgqIOordVpNEVixi6HdjcAnUzithAhR8VIRb3AQBzE8nsEpSWlwAAAABJRU5ErkJggg==',
			'9AA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQytrK6OjoIIIiJtLo2hAIEwM7adrUaStTV0VFhSG5j9UVpC5gKrJehlbRUNfQgAZkMQGweQEodohMAYuhuIU1AGIespsHKvyoCLG4DwDgA807RfHOJAAAAABJRU5ErkJggg==',
			'818D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjAGMDo6OgQgiQW0sgawNgQ6iKCoYwCrE0Fy39KoVVGrQldmTUNyH5o6qHkMGOZhE4PpRXYL0CWh6G4eqPCjIsTiPgDcAcjHuovy6gAAAABJRU5ErkJggg==',
			'ADB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGRoCkMRYA0RaWRsdGpHFRKaINLo2BLQiiwW0AsUaHaYEILkvaum0lamhq6KikNwHUefogKw3NBRkXmBoCLp5QJeg2QFyC5oYppsHKvyoCLG4DwDFxtAvhDK1/wAAAABJRU5ErkJggg==',
			'1E69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaY6IImxOog0MDo6BAQgiYkCxVgbHIEksl6QGCNMDOyklVlTw5ZOXRUVhuQ+sDpHh6mYegMasIhh2IHhlhBMNw9U+FERYnEfAIIfyJOfwT2QAAAAAElFTkSuQmCC',
			'CD44' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WENEQxgaHRoCkMREWkVaGVodGpHFAhpFGh2mOrSiiDUAxQIdpgQguS9q1bSVmZlZUVFI7gOpc210dEDX6xoaGBqCbgc2t6CJYXPzQIUfFSEW9wEAPdvQRVUO7+IAAAAASUVORK5CYII=',
			'EDF9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA6Y6IIkFNIi0sjYwBASgijW6NjA6iOAWAzspNGraytTQVVFhSO6DqGOYiqmXoQGLGLodGG4BuxloHrKbByr8qAixuA8AwD/Nc6s2QLUAAAAASUVORK5CYII=',
			'23DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANYQ1hDGaYGIImJTBFpZW10CBBBEgtoZWh0bQh0YEHW3crQygoUQ3HftFVhS1dFZqG4LwBFHRgyOkDMQ3FLA6YdIg2YbgkNxXTzQIUfFSEW9wEAMx7La/9pQzgAAAAASUVORK5CYII=',
			'4B38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37poiGMIYyTHVAFgsRaWVtdAgIQBJjDBFpdGgIdBBBEmOdItLKgFAHdtK0aVPDVk1dNTULyX0BqOrAMDQU0zyGKVjFMNyC1c0DFX7Ug1jcBwCXjs1yA4pb4QAAAABJRU5ErkJggg==',
			'F531' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxlDGVqRxQIaRBpYGx2moosByVA0sRCGRgeYXrCTQqOmLl01ddVSZPcFNABVIdQhxBoC0O3FIsbayoqhlzEE6ObQgEEQflSEWNwHAApuzp0XC3MeAAAAAElFTkSuQmCC',
			'2C63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxmA0AFJTGQKa6Ojo6NDAJJYQKtIg2uDQ4MIsm6gGCtIDtl906atWjp11dIsZPcFANU5OjQgm8foANIbgGIeawPIDlQxoCoMt4SGYrp5oMKPihCL+wD2OczVSs4B2gAAAABJRU5ErkJggg==',
			'797D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0MdkEVbWVsZGgIdAlDERBodgGIiyGJTgGKNjjAxiJuili7NWroyaxqS+xgdGAMdpjCi6GVtYGh0CEAVE2lgAZqGKhbQwNrKCjQhAEUM6OYGRlQ3D1D4URFicR8AO6LLRONSaE4AAAAASUVORK5CYII=',
			'229D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6NgQ6iCDrbmVAFoO4adqqpSszI7OmIbsvgGEKQwiqXkYHoCiaeaxAUUY0MRGQKJpbQkNFQx3Q3DxQ4UdFiMV9AOQvyk3Hx2ogAAAAAElFTkSuQmCC',
			'7870' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1pRRFtZgfyAqQ4oYiKNDg0BAQHIYlOA6hodHUSQ3Re1MmzV0pVZ05Dcx+gAVDeFEaYODFkbgOYFoIqJAMUcHRhQ7AhoYG1lbWBAcUtAA9DNQBcNhvCjIsTiPgAdIswGds3VfQAAAABJRU5ErkJggg==',
			'8B38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WANEQxhDGaY6IImJTBFpZW10CAhAEgtoFWl0aAh0EEFTx4BQB3bS0qipYaumrpqaheQ+NHU4zcNlB7pbsLl5oMKPihCL+wBP283u0qHn/wAAAABJRU5ErkJggg==',
			'2999' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEHWjSoGcdO0pUszM6OiwpDdF8AY6BASMBVZL6MDQ6NDQ0ADshhrA0ujY0MAih0iDZhuCQ3FdPNAhR8VIRb3AQBvBcueLT3dbgAAAABJRU5ErkJggg==',
			'188F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYHVhbGR0dHZDViTqINLo2BDqg6kVRB3bSyqyVYatCV4ZmIbmPEYt5jFjNI2gHxC0hYDejiA1U+FERYnEfABzexoF6dckvAAAAAElFTkSuQmCC',
			'B98A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpdG0ICAhAUSfS6Ojo6CCC5L7QqKVLs0JXZk1Dcl/AFMZAJHVQ8xiA5gWGhqCIsYDEUNWB3YKqF+JmRhSxgQo/KkIs7gMAbOXNA+EX5OgAAAAASUVORK5CYII=',
			'9C08' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEx1QBITmcLa6BDKEBCAJBbQKtLg6OjoIIImxtoQAFMHdtK0qdNWLV0VNTULyX2srijqIBCsNxDFPAEsdmBzCzY3D1T4URFicR8ArzjMgC1+ssQAAAAASUVORK5CYII=',
			'B7D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGVqRxQKmMDS6NjpMdUAWawWKNQQEBKCqa2VtCHQQQXJfaNSqaUtXRWZNQ3IfUF0AkjqoeYwOmGKsDawYdog0sKK5JTQAKIbm5oEKPypCLO4DAEKZzob6MxZwAAAAAElFTkSuQmCC',
			'BE80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGVqRxQKmiDQwOjpMdUAWaxVpYG0ICAjAUOfoIILkvtCoqWGrQldmTUNyH5o6JPMCsYhhswPVLdjcPFDhR0WIxX0ADPjM6lk1WPEAAAAASUVORK5CYII=',
			'B744' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQx0aHRoCkMQCpjA0OrQ6NKKItQLFpjq0oqlrZQh0mBKA5L7QqFXTVmZmRUUhuQ+oLoC10dEB1TxGB9bQwNAQFDHWBgYMt4hgiIUGYIoNVPhREWJxHwCDWtBW0uz40QAAAABJRU5ErkJggg==',
			'A765' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsRHDMAhFoWADZZ9PoZ4UNJoGF97AzgYuoimjEsUukzvzu3cf7h3UTxN0p/zFj/FwOLslJkaLqiL3ykZLjZnZSqsEVyS/dvTXsb9bS36jZ6KIknbdGRI2MRvXJJ6YWQlWmH0xctpxg//9MBd+H2gky+kmeTwsAAAAAElFTkSuQmCC',
			'ED58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHaY6IIkFNIi0sjYwBASgijW6NjA6iKCLTYWrAzspNGraytTMrKlZSO4DqXNoCMAwz6EhENM8TLFWRkcHFL0gNzOEMqC4eaDCj4oQi/sA2O3OUiWloksAAAAASUVORK5CYII=',
			'E02D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUMdkMQCGhhDGB0dHQJQxFhbWRsCHURQxEQaHRBiYCeFRk1bmbUyM2sakvvA6loZMfVOQRdjbWUIQBcDusWBEcUtIDezhgaiuHmgwo+KEIv7ADjfy1vmwahaAAAAAElFTkSuQmCC',
			'1F28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxOog0MDo6BAQgiYkCxVgbAoEksl4QLwCmDuyklVlTw0BEFpL7wOpaGVDMA4tNYcQ0LwBTjNEBVa9oCNAtoQEobh6o8KMixOI+AAqIyNH9+ykoAAAAAElFTkSuQmCC',
			'A023' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AMAwE7cIbZCBnAxe4YYRMEYpsgNggRZgSi8oJlCDwd6fX62TYL5fhT3nFDxkEFJQdI8EJY2RxLKxUKEsOjkkJCxsT5zfXraWWanJ+Z69A9nuqxlYY9qiYzcDMhbFzEWuRSuf81f8ezI3fAUOgzHvfqXg6AAAAAElFTkSuQmCC',
			'7340' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNZQxgaHVpRRFtFWhlaHaY6oIgBVU11CAhAFpsCFA10dBBBdl/UqrCVmZlZ05Dcx+jA0MraCFcHhqwNDI2uoYEoYkB2o0Mjqh0BDSIQm1HEsLh5gMKPihCL+wDI38zPsawJwAAAAABJRU5ErkJggg==',
			'F52D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUMdkMQCGkQaGB0dHQLQxFgbAh1EUMVCGBBiYCeFRk1dumplZtY0JPcBzWl0aGVE0wsUm4IuJtLoEIAuxgrUyYjmFsYQ1tBAFDcPVPhREWJxHwAGCcwRR021NAAAAABJRU5ErkJggg==',
			'92BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0ijS6NgQ6iKCIMTS6AtWJILlv2tRVS5eGrsyahuQ+VleGKawIdRDYyhDAimaeQCujA7oY0C0N6G5hDRANdUVz80CFHxUhFvcBAHF+y3s1qF1XAAAAAElFTkSuQmCC',
			'C687' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGUNDkMREWllbGR0dGkSQxAIaRRpZGwJQxYA8kLoAJPdFrZoWtip01cosJPcFNIiCzGtlQNXb6NoQMIUBzQ6gWAADhlscHbC4GUVsoMKPihCL+wDttMvWGqFSEwAAAABJRU5ErkJggg==',
			'4B55' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpI37poiGsIY6hgYgi4WItLI2MDogq2MMEWl0RRNjnQJUN5XR1QHJfdOmTQ1bmpkZFYXkvgCgOiDZIIKkNzRUpNEBTYxhCsiOQAc0sVZGR4cAFPcB3cwQyjDVYTCEH/UgFvcBAAGGy4xMxQp6AAAAAElFTkSuQmCC',
			'924B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUMdkMREprC2MrQ6OgQgiQW0ijQ6THV0EEERY2h0CISrAztp2tRVS1dmZoZmIbmP1ZVhCmsjqnkMrQwBrKGBKOYJtDI6AN2CIgZ0SwMDml7WANFQBzQ3D1T4URFicR8A8EzL3gNwUYkAAAAASUVORK5CYII=',
			'FF00' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMLQiiwU0iDQwhDJMdUATY3R0CAhAE2NtCHQQQXJfaNTUsKWrIrOmIbkPTR1eMWx2YHMLA5qbByr8qAixuA8Ag9bNST7lrcEAAAAASUVORK5CYII=',
			'D298' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtCHQQQRFjAIoFwNSBnRS1dNXSlZlRU7OQ3AdUN4UhJADNPIYABgzzGB0Y0cWmsDaguyU0QDTUAc3NAxV+VIRY3AcASWTNzgXvUFUAAAAASUVORK5CYII=',
			'D0CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHUMdkMQCpjCGMDoEOgQgi7WytrI2CDqIoIiJNLo2MMLUgZ0UtXTaytRVK0OzkNyHpg5FTISQHVjcgs3NAxV+VIRY3AcAqKzMjBZ+gJoAAAAASUVORK5CYII=',
			'BB16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQximMEx1QBILmCLSyhDCEBCALNYq0ugYwugggK5uCqMDsvtCo6aGrZq2MjULyX1QdRjmOQD1ihASA+tFdQvIzYyhDihuHqjwoyLE4j4APHDNKap7mm0AAAAASUVORK5CYII=',
			'E3BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGUMDkMQCGkRaWRsdHRhQxBgaXRsC0cWQ1YGdFBq1Kmxp6MrQLCT3oanDZx4WMUy3YHPzQIUfFSEW9wEA0d/L7SjWJl8AAAAASUVORK5CYII=',
			'9A48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHaY6IImJTGEMYWh1CAhAEgtoZW1lmOroIIIiJtLoEAhXB3bStKnTVmZmZk3NQnIfq6tIo2sjqnkMraKhrqGBKOYJgMxrRLVDZApIDFUvawBYDMXNAxV+VIRY3AcA4B/NwiB8r7YAAAAASUVORK5CYII=',
			'BC99' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGaY6IIkFTGFtdHR0CAhAFmsVaXBtCHQQQVEn0sCKEAM7KTRq2qqVmVFRYUjuA6ljCAmYKoJmHpBsQBdzbAhAswPTLdjcPFDhR0WIxX0AdiLODY+g53IAAAAASUVORK5CYII=',
			'560F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQximMIaGIIkFNLC2MoQyOjCgiIk0Mjo6oogFBog0sDYEwsTATgqbNi1s6arI0Cxk97WKtiKpg4qJNLqiiQUAxRzR7BCZgukW1gCwm1HNG6DwoyLE4j4AEu3JgpuG2usAAAAASUVORK5CYII=',
			'115C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHaYGIImxOjAGsDYwBIggiYk6sALFGB1Y0PVOBZJI7luZtSpqaWZmFrL7QOoYGgId0O3FJsYKFEO3g9HRAdUtIayhDKEMKG4eqPCjIsTiPgAS+cW/LRMEIAAAAABJRU5ErkJggg==',
			'D75F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHUNDkMQCpjA0ujYwOiCrC2jFKtbKOhUuBnZS1NJV05ZmZoZmIbkPqC6AoSEQTS9IH7oYawMrutgUkQZGR0cUsdAAkQaGUFS3DFT4URFicR8AO8nLJnz6qJAAAAAASUVORK5CYII=',
			'18A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQximMLQii7E6sLYyhDJMdUASE3UQaXR0dAgIQNHL2sraEOggguS+lVkrw5auisyahuQ+NHVQMZFG11AsYg0BWOwIQHVLCGMIUAzFzQMVflSEWNwHACSCyhAdTGugAAAAAElFTkSuQmCC',
			'D4AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYWhmmMIY6IIkFTGGYyhDK6BCALNYKFHF0dBBBEWN0ZW0IhImBnRS1FAhWRWZNQ3JfQKtIK5I6qJhoqGsouhgDpropEDFkt4DcDBRDcfNAhR8VIRb3AQAgP80zNZsEzwAAAABJRU5ErkJggg==',
			'0D91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgDRFoZHR2mIouJTBFpdG0ICEUWC2gFi8H0gp0UtXTayszMqKXI7gOpcwgJaEXX69CAKgaywxFNDOoWFDGom0MDBkH4URFicR8AbdbMdmcA1vAAAAAASUVORK5CYII=',
			'A87F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0NDkMRYA1hbGRoCHZDViUwRaXRAEwtoBaprdISJgZ0UtXRl2KqlK0OzkNwHVjeFEUVvaCjQvABGNPNEgKahi7G2sjagiwHdjCY2UOFHRYjFfQDoI8pOT7sNVQAAAABJRU5ErkJggg==',
			'A277' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0NDkMRYA1hbGRoCGkSQxESmiDQ6oIkFtDI0OoBFEe6LWroKBFdmIbkPqG4KELYi2xsayhAAhFMYUMwDusYBKIoixtrACnQlqphoqCua2ECFHxUhFvcBAJvDzEZFBppxAAAAAElFTkSuQmCC',
			'80F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0NDkMREpjCGsIJoJLGAVtZWdDGRKSKNriA5JPctjZq2MjV01cosJPdB1bUyoJgHFpvCgGlHAEMDulsYHTDcjCY2UOFHRYjFfQAOeMsF5pClYAAAAABJRU5ErkJggg==',
			'8914' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQximMDQEIImJTGFtZQhhaEQWC2gVaXQMYWhFVSfS6DCFYUoAkvuWRi1dmjVtVVQUkvtEpjAGOkxhdEA1jwGolzE0BEWMBWQeplvQxEBuZgx1QBEbqPCjIsTiPgBH5s33jfKNlAAAAABJRU5ErkJggg==',
			'654D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxkaHUMdkMREpog0MLQ6OgQgiQW0AMWmOjqIIIs1iIQwBMLFwE6KjJq6dGVmZtY0JPeFTGFodG1E09sKFAsNRBMTaXRAUycyhRWoEtUtrAGMIehuHqjwoyLE4j4AsU/MqZdqlmAAAAAASUVORK5CYII=',
			'749D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZWhlCGUMdkEVbGaYyOjo6BKCKhbI2BDqIIItNYXRFEoO4KWrp0pWZkVnTkNzH6CDSyhCCqpe1QRRoJ6oYkN3KiCYWABJDc0tAAxY3D1D4URFicR8Au+jKYVnqc0QAAAAASUVORK5CYII=',
			'5E0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIaGIIkFNIg0MIQyOjCgiTE6OqKIBQaINLA2BMLEwE4KmzY1bOmqyNAsZPe1oqjDKRbQimmHyBRMt7AGgN2Mat4AhR8VIRb3AQDIpsk2Rl7EhAAAAABJRU5ErkJggg==',
			'BD4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxgaHUMdkMQCpoi0MrQ6OgQgi7WKNDpMdXQQQVXX6BAIVwd2UmjUtJWZmZmhWUjuA6lzbcQ0zzU0ENU8kB2NGHa0MqDpxebmgQo/KkIs7gMAafTOx2hkDLIAAAAASUVORK5CYII=',
			'EF0D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMIY6IIkFNIg0MIQyOgSgiTE6OjqIoImxNgTCxMBOCo2aGrZ0VWTWNCT3oanDK4bNDnS3hIYAxdDcPFDhR0WIxX0AJoLMJaRLXGgAAAAASUVORK5CYII=',
			'DC7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDA0MDkMQCprA2OjQEOiCrC2gVacAmxtDoCBMDOylq6bRVq5auDM1Cch9Y3RRGTL0BmGKODmhiQLe4NqCKgd3cwIji5oEKPypCLO4DAMkfzGVp4eKOAAAAAElFTkSuQmCC',
			'60E1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHVqRxUSmMIawNjBMRRYLaGFtBYqFoog1iDS6NjDA9IKdFBk1bWVq6KqlyO4LmYKiDqK3FZsY2A5sbkERg7o5NGAQhB8VIRb3AQA9GcuB9L+9TgAAAABJRU5ErkJggg==',
			'9C86' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGaY6IImJTGFtdHR0CAhAEgtoFWlwbQh0EEATYwQqRHbftKnTVq0KXZmaheQ+VlewOhTzGIB6WYHmiSCJCUDtECHgFmxuHqjwoyLE4j4At3jLyZyoW8wAAAAASUVORK5CYII=',
			'DC89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGaY6IIkFTGFtdHR0CAhAFmsVaXBtCHQQQRNjBCoUQXJf1NJpq1aFrooKQ3IfRJ3DVHS9rA0BDehirg0BqHZgcQs2Nw9U+FERYnEfAHUVzhCfseuZAAAAAElFTkSuQmCC',
			'D7A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMDQEIIkFTGFodAhlaEQRa2VodHR0aEUTa2UFqg5Acl/U0lXTlq6KiopCch9QXQBrQ6ADql5GB9bQwNAQFDHWBqB5aG4RwRALDcAUG6jwoyLE4j4APgLQVJsCOvAAAAAASUVORK5CYII=',
			'0275' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QMQ6AIAxFy8AN6n3qwF4SWThNGbgBHsFBTmnHoo6a0Lf9/J+8FPrjBGbiFz9HbvMpJjaZZ19BItkeNix0y7hCobIGMn756MqZs/HTXlMExy0rQ4ZNbRQcXcQLsPVztKQgsNME//uQF78LsqrK41X+xwUAAAAASUVORK5CYII=',
			'2B8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaYGIImJTBFpZXR0CBBBEgtoFWl0bQh0YEHW3QpS5+iA4r5pU8NWha7MQnFfAIo6MGR0gJiH4pYGTDtEGjDdEhqK6eaBCj8qQizuAwAxisqi54yLnwAAAABJRU5ErkJggg==',
			'042C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QsQ2AMAwE3xLZIOxjCnpTpGEDmILGG4QRUsCUhM4WlCDwdye9/mTsl1vwp7ziRwxFwiqGBcFKHUs0LGaksAzcGCZKPSqzfmMpZd+m2fqJRoUSw3XbxNmzuqEQchvV5Ww6l9M5JHHOX/3vwdz4HfQ2yZVQUgxqAAAAAElFTkSuQmCC',
			'2EBB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUMdkMREpog0sDY6OgQgiQW0AsUaAh1EkHW3oqiDuGna1LCloStDs5DdF4BpHqMDpnmsDZhiIg2YekNDMd08UOFHRYjFfQDVissdSq39YwAAAABJRU5ErkJggg==',
			'368E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUMDkMQCprC2Mjo6OqCobBVpZG0IRBWbItKApA7spJVR08JWha4MzUJ23xRRrOa5opuHRQybW7C5eaDCj4oQi/sAh6zJWcovhk8AAAAASUVORK5CYII=',
			'4743' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37poiGOjQ6hDogi4UwNDq0OjoEIIkxgsSmOjSIIImxTmFoZQh0aAhAct+0aaumrczMWpqF5L6AKQwBrI1wdWAYGsrowBoagGIewxTWBqAtaGJAXiOqWyBiaG4eqPCjHsTiPgD1ss2Yjc2yUwAAAABJRU5ErkJggg==',
			'F20D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZQximMIY6IIkFNLC2MoQyOgSgiIk0Ojo6OoigiDE0ujYEwsTATgqNWrV06arIrGlI7gOqm8KKUAcTC8AUY3RgxLCDtQHTLaKhDmhuHqjwoyLE4j4AMB/MMEIhPvAAAAAASUVORK5CYII=',
			'9C53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHUIdkMREprA2ujYwOgQgiQW0ijS4guTQxFinAmkk902bOm3V0syspVlI7mN1BekKaEA2j6EVIoZsngDYDlQxkFscHR1R3AJyMwMQIrt5oMKPihCL+wBjtM0Trsua6AAAAABJRU5ErkJggg==',
			'CBEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WENEQ1hDHVqRxURaRVpZGximOiCJBTSKNLo2MAQEIIs1gNQxOogguS9q1dSwpaErs6YhuQ9NHUwMaB5jaAiGHajqIG5BFYO42RFFbKDCj4oQi/sAvDfLrBAJPhwAAAAASUVORK5CYII=',
			'9BDA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGVqRxUSmiLSyNjpMdUASC2gVaXRtCAgIQBVrZW0IdBBBct+0qVPDlq6KzJqG5D5WVxR1EAg2LzA0BElMACKGog7iFkcUMYibGVHNG6DwoyLE4j4A1C/MfOSyGwcAAAAASUVORK5CYII=',
			'7C8A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGVpRRFtZGx0dHaY6oIiJNLg2BAQEIItNEWlgBCoUQXZf1LRVq0JXZk1Dch+jA4o6MGRtEAHiwNAQJDGRBpAdgSjqAhpAbnFEEwO5mRFFbKDCj4oQi/sAe/nLkwjqoZ4AAAAASUVORK5CYII=',
			'0D3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1EQxhDGUNDkMRYA0RaWRsdHZDViUwRaXRoCEQRC2gFiiHUgZ0UtXTayqypK0OzkNyHpg4hhmYeNjuwuQXqZhSxgQo/KkIs7gMAxK7K9Mg5kJsAAAAASUVORK5CYII=',
			'958D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMdkMREpog0MDo6OgQgiQW0ijSwNgQ6iKCKhYDUiSC5b9rUqUtXha7MmobkPlZXhkZHhDoIbGVodEUzT6BVBENMZAprK7pbWAMYQ9DdPFDhR0WIxX0AXMzKuLXNAvQAAAAASUVORK5CYII=',
			'052F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUNDkMRYA0QaGB0dHZDViUwRaWBtCEQRC2gVCWFAiIGdFLV06tJVKzNDs5DcF9DK0OjQyoimFyg2hRHdjkaHAFQx1gBWoE5UMSA/hDUU1S0DFX5UhFjcBwD5lcisG/tg/AAAAABJRU5ErkJggg==',
			'3131' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGVqRxQKmMAawNjpMRVHZyhoAlAlFEZvCEMDQ6ADTC3bSyqhVUaumrlqK4j5UdVDzGEDmERQLAOplRdMrGsAaCnRzaMAgCD8qQizuAwALvsqKU+kKfAAAAABJRU5ErkJggg==',
			'3144' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYAhgaHRoCkMQCpjAGMLQ6NCKLMbSyBjBMdWhFEZsC1BvoMCUAyX0ro1ZFrczMiopCdh9QHWujowOqeUCx0MDQEDQxTLdgiokCdaKLDVT4URFicR8AS67MT9yZPBoAAAAASUVORK5CYII=',
			'63DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANYQ1hDGUNDkMREpoi0sjY6OiCrC2hhaHRtCEQVa2BoZUWIgZ0UGbUqbOmqyNAsJPeFTEFRB9HbisU8LGLY3AJ1M4rYQIUfFSEW9wEApYrK4fBPzV4AAAAASUVORK5CYII=',
			'6148' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHaY6IImJTGEMYGh1CAhAEgtoAaqc6ugggizWANQbCFcHdlJk1KqolZlZU7OQ3BcyhSGAtRHNvFagWGggqnmtILeg2iEyBew+FL2sQJ3obh6o8KMixOI+ABUwy1VxJeLQAAAAAElFTkSuQmCC',
			'114D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUMdkMRYHRgDGFodHQKQxEQdWAMYpjo6iKDrDYSLgZ20MmtV1MrMzKxpSO4DqWNtxNTLGhqIaR4WdSAxFLeEsIaiu3mgwo+KEIv7AHKJxtzQ2JbJAAAAAElFTkSuQmCC',
			'5BF8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA6Y6IIkFNIi0sjYwBASgijW6NjA6iCCJBQagqAM7KWza1LCloaumZiG7rxXTPKAYhnkBWMREpmDqZQ0AurmBAcXNAxV+VIRY3AcAckDMOzBARA8AAAAASUVORK5CYII=',
			'5B0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQximMLQiiwU0iLQyhDJMdUAVa3R0dAgIQBILDBBpZW0IdBBBcl/YtKlhS1dFZk1Ddl8rijqYWKNrQ2BoCLIdrSA7HFHUiUwBuYURRYw1AORmVLGBCj8qQizuAwAtrsvnww45PgAAAABJRU5ErkJggg==',
			'EEC2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHaY6IIkFNIg0MDoEBASgibE2CDqIYIgxNIgguS80amrYUiAdheQ+qLpGdDuAYq0MGGICU9DFQG7BdLNjaMggCD8qQizuAwBDcMz7cr0oUgAAAABJRU5ErkJggg==',
			'6192' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsQ2AMBADnSIbZKCwgZGSgmzAFo8EG4QhyJQEqg9QgsRbcnGNT49yO8Gf8omfJYiI1SvmsqHpPKkYZ0srvXeaCSqjOOU3pJK2sZbyC7luBE56g0tlR1+YEWY0LjhdWmcbEU0MP/jfi3nw2wHinMpRHEFcWgAAAABJRU5ErkJggg==',
			'CB59' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQ1hDHaY6IImJtIq0sjYwBAQgiQU0ijS6NjA6iCCLAVWyToWLgZ0UtWpq2NLMrKgwJPeB1AHJqWh6Gx1AJIYdASh2gNzC6OiA4haQmxlCGVDcPFDhR0WIxX0Axb7MvDVaGhQAAAAASUVORK5CYII=',
			'35E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANEQ1lDHaY6IIkFTBFpYG1gCAhAVtkKEmN0EEAWmyISAhJDdt/KqKlLl4auTM1Cdt8UhkbXBkY088BiDiKodmCIBUxhbUV3i2gAYwi6mwcq/KgIsbgPALUuyxFTusOwAAAAAElFTkSuQmCC',
			'239B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGUMdkMREpoi0Mjo6OgQgiQW0MjS6NgQ6iCDrbmVoZQWKBSC7b9qqsJWZkaFZyO4LAKoMCUQxj9GBodEBzTzWBoZGRzQxkQZMt4SGYrp5oMKPihCL+wAyX8qdtSxVrwAAAABJRU5ErkJggg==',
			'7EC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHUMDkEVbRRoYHQIdGNDEWBsEUcWmgMQYXR2Q3Rc1NWzpqpVRUUjuY3QAqWNoEEHSy9qAKSbSALEDWSygAeSWgIAAFDGQmx2mOgyC8KMixOI+APiQyq0EHfOvAAAAAElFTkSuQmCC',
			'494C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjCGMDQ6TA1AFgthbWVodQgQQRJjDBEBqnJ0YEESY50CFAt0dEB237RpS5dmZmZmIbsvYApjoGsjXB0YhoYyNLqGBjqguoWl0aER1Q6GKUC3NKK6BaubByr8qAexuA8ADzDMLpWHafUAAAAASUVORK5CYII=',
			'FBBC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGaYGIIkFNIi0sjY6BIigijW6NgQ6sGCoc3RAdl9o1NSwpaErs5Ddh6YOxTxsYph2oLsF080DFX5UhFjcBwDp082lMqZwpQAAAABJRU5ErkJggg==',
			'0F3E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1DGUMDkMRYA0QaWBsdHZDViUwRAZKBKGIBrUAxhDqwk6KWTg1bNXVlaBaS+9DUIcTQzMNmBza3MDqINDCiuXmgwo+KEIv7AOwLyl7BnV4IAAAAAElFTkSuQmCC',
			'4BC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37poiGMIQ6tKKIhYi0MjoETHVAEmMMEWl0bRAICEASY50i0srawOggguS+adOmhi1dtTJrGpL7AlDVgWFoKMg8VDGGKZh2AMUw3ILVzQMVftSDWNwHAPtyzCdmgaokAAAAAElFTkSuQmCC',
			'5D05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQximMIYGIIkFNIi0MoQyOjCgijU6OjqiiAUGiDS6NgS6OiC5L2zatJWpqyKjopDd1wpSBzQB2WYsYgGtEDuQxUSmgNzCEIDsPtYAkJsZpjoMgvCjIsTiPgB9O8x631HkEgAAAABJRU5ErkJggg==',
			'5798' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGaY6IIkFNDA0Ojo6BASgibk2BDqIIIkFBjC0sjYEwNSBnRQ2bdW0lZlRU7OQ3dfKEMAQEoBiHkMrowMDmnkBQNMY0cREpog0MKK5hTUAqALNzQMVflSEWNwHAL+2zF32r5xFAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>