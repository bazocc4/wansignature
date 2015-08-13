<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php echo $content_for_layout; ?>
<script>
    $(document).ready(function(){
        // initialize bootstrap tooltip !!
        if( $.isFunction($.fn.tooltip) )
        {
            $('[data-toggle=tooltip]').not('[data-original-title]').tooltip({
                container: ($('#cboxContent').length>0&&$('#cboxContent').is(':visible')?'#cboxContent':'body'),
                html: true,
            }).each(function(){
                $(this).attr('title' , $(this).attr('alt') );
            });
        }
    });
</script>