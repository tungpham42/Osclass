<?php
    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */

    $users            = __get("users") ;
    $max              = __get("max") ;
    $item             = __get("item") ;
    $users_by_country = __get("users_by_country") ;
    $users_by_region  = __get("users_by_region") ;
    $latest_users     = __get("latest_users") ;
    
    switch(Params::getParam('type_stat')){
        case 'week':
            $type_stat = __('Last 10 weeks');
            break;
        case 'month':
            $type_stat = __('Last 10 months');
            break;
        default:
            $type_stat = __('Last 10 days');
    }


    osc_add_filter('render-wrapper','render_offset');
    function render_offset(){
        return 'row-offset';
    }
    osc_add_hook('admin_page_header','customPageHeader');
    function customPageHeader(){ ?>
        <h1><?php _e('Statistics') ; ?></h1>
    <?php
    }
    function customHead(){
    $users            = __get("users") ;
    $max              = __get("max") ;
    $item             = __get("item") ;
    $users_by_country = __get("users_by_country") ;
    $users_by_region  = __get("users_by_region") ;
    $latest_users     = __get("latest_users") ;
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <?php if(count($users)>0) { ?>
    <script type="text/javascript">
        // Load the Visualization API and the piechart package.
        google.load('visualization', '1', {'packages':['corechart']}) ;

        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table, 
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {
            var data = new google.visualization.DataTable();

            data.addColumn('string', '<?php _e('Date') ; ?>',0,1);
            data.addColumn('number', '<?php _e('New users') ; ?>');
            <?php $k = 0 ;
            echo "data.addRows(" . count($users) . ");" ;
            foreach($users as $date => $num) {
                echo "data.setValue(" . $k . ', 0, "'. $date . '");' ;
                echo "data.setValue(" . $k . ", 1, " . $num . ");" ;
                $k++ ;
            }
            ?>

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.AreaChart(document.getElementById('placeholder'));
            chart.draw(data, {
                    colors:['#058dc7','#e6f4fa'],
                        areaOpacity: 0.1,
                        lineWidth:3,
                        hAxis: {
                        gridlines:{
                            color: '#333',
                            count: 3
                        },
                        viewWindow:'explicit',
                        showTextEvery: 2,
                        slantedText: false,
                        textStyle:{
                            color: '#058dc7',
                            fontSize: 10
                        }
                        },
                        vAxis: {
                            gridlines:{
                                color: '#DDD',
                                count: 4,
                                style: 'dooted'
                            },
                            viewWindow:'explicit',
                            baselineColor:'#bababa'

                        },
                        pointSize: 6,
                        legend: 'none',
                        chartArea:{
                            left:10,
                            top:10,
                            width:"95%",
                            height:"80%"
                        }
                    });

            var data_country = new google.visualization.DataTable();
            data_country.addColumn('string', '<?php _e('Country') ; ?>');
            data_country.addColumn('number', '<?php _e('Users per country') ; ?>');
            data_country.addRows(<?php echo count($users_by_country) ; ?>);
            <?php foreach($users_by_country as $k => $v) {
                echo "data_country.setValue(" . $k . ", 0, '" . ( ( $v['s_country'] == NULL ) ? __('Unknown') : $v['s_country'] ) . "');" ;
                echo "data_country.setValue(" . $k . ", 1, " . $v['num'] . ");" ;
            } ?>

            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById('by_country')).draw(data_country, {title:"<?php _e('Users per country') ; ?>"});

            var data_region = new google.visualization.DataTable();
            data_region.addColumn('string', '<?php _e('Region') ; ?>');
            data_region.addColumn('number', '<?php _e('Users per region') ; ?>');
            data_region.addRows(<?php echo count($users_by_region) ; ?>);
            <?php foreach($users_by_region as $k => $v) {
                echo "data_region.setValue(" . $k . ", 0, '" . ( ( $v['s_region'] == NULL ) ? __('Unknown') : $v['s_region'] ) . "');" ;
                echo "data_region.setValue(" . $k . ", 1, " . $v['num'] . ");" ;
            } ?>

            // Create and draw the visualization.
            new google.visualization.PieChart(document.getElementById('by_region')).draw(data_region, {title:"<?php _e('Users per region') ; ?>"});
        }
    </script>
<?php }
    }
    osc_add_hook('admin_header', 'customHead');
?>
<?php osc_current_admin_theme_path( 'parts/header.php' ) ; ?>
<div class="grid-system">
    <div class="grid-row grid-first-row grid-100 no-bottom-margin">
        <div class="row-wrapper">
                <h2 class="render-title"><?php _e('User Statistics'); ?></h2>
        </div>
    </div>
    <div class="grid-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('New users'); ?>
                    <select class="widget-box-selector select-box-big input-medium">
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=users&amp;type_stat=day"><?php _e('Last 10 days') ; ?></option>
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=users&amp;type_stat=week"><?php _e('Last 10 weeks') ; ?></option>
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=users&amp;type_stat=month"><?php _e('Last 10 months') ; ?></option>
                    </select>
                    </h3>
                </div>
                <div class="widget-box-content">
                    <b class="stats-title"><?php _e('_'); ?></b>
                    <div class="stats-detail"><?php echo $type_stat; ?></div>
                    <div id="placeholder" class="graph-placeholder" style="height:150px">
                        <?php if( count($items) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Users per country'); ?>
                    <select class="widget-box-selector select-box-big input-medium">
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=items&amp;type_stat=day"><?php _e('Last 10 days') ; ?></option>
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=items&amp;type_stat=week"><?php _e('Last 10 weeks') ; ?></option>
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=items&amp;type_stat=month"><?php _e('Last 10 months') ; ?></option>
                    </select>
                    </h3>
                </div>
                <div class="widget-box-content">
                    <b class="stats-title"><?php _e('_'); ?></b>
                    <div class="stats-detail"><?php echo $type_stat; ?></div>
                    <div id="by_country" class="graph-placeholder" style="height:150px">
                        <?php if( count($reports) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Users per region'); ?>
                    <select class="widget-box-selector select-box-big input-medium">
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=items&amp;type_stat=day"><?php _e('Last 10 days') ; ?></option>
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=items&amp;type_stat=week"><?php _e('Last 10 weeks') ; ?></option>
                        <option value="<?php echo osc_admin_base_url(true); ?>?page=stats&amp;action=items&amp;type_stat=month"><?php _e('Last 10 months') ; ?></option>
                    </select>
                    </h3>
                </div>
                <div class="widget-box-content">
                    <b class="stats-title"><?php _e('_'); ?></b>
                    <div class="stats-detail"><?php echo $type_stat; ?></div>
                    <div id="by_region" class="graph-placeholder" style="height:150px">
                        <?php if( count($reports) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3><?php _e('Latest users on the web') ; ?></h3></div>
                <div class="widget-box-content">
                    <?php if( count($latest_users) > 0 ) { ?>
                    <table class="table" cellpadding="0" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php _e('E-Mail') ; ?></th>
                            <th><?php _e('Name') ; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($latest_users as $u) { ?>
                        <tr>
                            <td><a href="<?php echo osc_admin_base_url(true); ?>?page=users&amp;action=edit&amp;id=<?php echo $u['pk_i_id'] ; ?>"><?php echo $u['pk_i_id'] ; ?></a></td>
                            <td><a href="<?php echo osc_admin_base_url(true); ?>?page=users&amp;action=edit&amp;id=<?php echo $u['pk_i_id'] ; ?>"><?php echo $u['s_email'] ; ?></a></td>
                            <td><a href="<?php echo osc_admin_base_url(true); ?>?page=users&amp;action=edit&amp;id=<?php echo $u['pk_i_id'] ; ?>"><?php echo $u['s_name'] ; ?></a></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <p><?php _e("There're no statistics yet") ; ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php osc_current_admin_theme_path( 'parts/footer.php' ) ; ?>