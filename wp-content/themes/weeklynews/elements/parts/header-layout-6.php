<?php
    global $mp_weeklynews;
?>
<!-- start:header-branding -->
<div id="header-branding">                
    <!-- start:container -->
    <div class="<?php echo ( isset($mp_weeklynews['_mp_header_type']) && ($mp_weeklynews['_mp_header_type'] == 'streched')) ? 'no-container' : 'container'; ?>">
        
        <!-- start:row -->
        <div class="row">
        
            <!-- start:col -->
            <div class="col-sm-6 col-md-6" itemscope="itemscope" itemtype="http://schema.org/Organization">
                <!-- start:logo -->
                <?php echo MipTheme_Util::miptheme_set_logo(); ?>
                <meta itemprop="name" content="<?php bloginfo('name')?>">
                <!-- end:logo -->
            </div>
            <!-- end:col -->
            
            <?php
                if ( isset($mp_weeklynews['_mp_header_weather_location']) ) {
            ?>
            <!-- start:col -->
            <div class="visible-md visible-lg col-md-6 text-right">
                <div class="weather" id="weather">
                    <i class="icon"></i>
                    <h3><span class="glyphicon glyphicon-map-marker"></span> <span class="location"></span> <span class="temp"></span></h3>
                    <span class="date"><?php if ( isset($mp_weeklynews['_mp_header_weather_show_date']) && (bool)$mp_weeklynews['_mp_header_weather_show_date']) echo date_i18n(MIPTHEME_DATE_DEFAULT); ?> <?php if ( isset($mp_weeklynews['_mp_header_weather_show_desc']) && (bool)$mp_weeklynews['_mp_header_weather_show_desc']) echo '<span class="desc"></span>'; ?></span>
                </div>
            </div>
            <!-- end:col -->
            <script>
                "use strict";
                var weather_widget      = true;
                var weather_lang        = '<?php echo esc_js( ( isset($mp_weeklynews['_mp_header_weather_lang']) && ($mp_weeklynews['_mp_header_weather_lang'] != '') ) ? $mp_weeklynews['_mp_header_weather_lang'] : '' ); ?>';
                var weather_location    = '<?php echo esc_js($mp_weeklynews['_mp_header_weather_location']); ?>';
                var weather_unit        = '<?php echo esc_js($mp_weeklynews['_mp_header_weather_temperature']); ?>';
            </script>
            <?php
                }
            ?>
        </div>
        <!-- end:row -->

    </div>
    <!-- end:container -->                    
</div>
<!-- end:header-branding -->