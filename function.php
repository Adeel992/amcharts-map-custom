<?php 
/*Maps function*/
add_shortcode('map_view', 'my_map_view');
function my_map_view() {
    ob_start();
?>

<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/none.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<div id="chartdiv"></div>

<div class="chardiv-content">
    <h2>We've got you covered in <br>over 5 countries</h2>
    <div class="icon-select">
          <i class="fa-solid fa-earth-asia"></i>
          
    <?php
    
       $args = array(
            'post_type' => 'map-locator',
            'posts_per_page' => -1
        );

        $map_loc_query = new WP_Query($args);
        ?>
       
        <select id="countrySelector" class="form-control">
            <option value="">Select a Country</option>
            <?php
             if ($map_loc_query->have_posts()) {
                while ($map_loc_query->have_posts()) : $map_loc_query->the_post();
                $country_name = get_field('country_name');
                $country_class = get_field('pin_class');
            ?>
            <option value="<?php echo $country_class; ?>"><?php echo $country_name; ?></option>
            <?php
                endwhile;
            }
            wp_reset_postdata(); 
         ?>
        </select>
      
    </div>

        <div id="country-title"></div>
        <div id="country-desc"></div>
        <div id="location-content" class="loc-content">
        <div id="city-img"></div>
        <div id="city-name"></div>
        <div id="city-description"></div>
    </div>
</div>

<script>
var markerData = [
      <?php
     
       $taxonomies = get_terms( array(
        	'taxonomy' => 'city',
        	'hide_empty' => false
        ) );
        
        if (!empty($taxonomies)) {
           
            foreach ($taxonomies as $city) {
                 $term_id = $city->term_id;
                 $latitude = get_field('latitude', 'city_' . $term_id);
                 $longitude = get_field('longitude', 'city_' . $term_id);
                 $pin_image = get_field('pin_image', 'city_' . $term_id);
                 $pin_class = get_field('pin_class', 'city_' . $term_id);
                 $location_img = get_field('location_image', 'city_' . $term_id);
               ?>
               {
                "groupId": "minZoom-2.5",
                "title": "<?php echo esc_html($city->name); ?>",
                "locationDescription": "<?php echo esc_html($city->description);?>",
                "latitude": <?php echo $latitude; ?>,
                "longitude": <?php echo $longitude; ?>,
                "imageURL": "https://dev.imarat.com.pk/wp-content/uploads/2023/11/map-pointer.svg",
                "locationimageURL": "<?php echo $location_img; ?>",
                "labelColor": "#000",
                "color": "#FF5733",
                "url": "javascript:void(0)",
                "className": "<?php echo $pin_class; ?>",
                },
               <?php
            }
           
        }
        
  ?>
];
var areasSettings = {
//     "selectedColor": "#71A195",
//    "color": "#71A195"
    "selectedColor": "#9D9D9C",
    "color": "#EEEEEE"
};

var map = AmCharts.makeChart("chartdiv", {
    "type": "map",
    "theme": "none",
    "projection": "miller",
  
    "zoomControl": {
        "homeButtonEnabled": false,
        "zoomControlEnabled": true,
        "panControlEnabled": false,
    },
    "homeButton": {
        "enabled": false
    },
    "smallMap": {
        "enabled": false
    },
    "dataProvider": {
        "map": "worldLow",
        "getAreasFromMap": true,
        "images": markerData
    },
    "areasSettings": {
        "autoZoom": true,
        "selectedColor": "#9D9D9C",
       "color": "#EEEEEE",
    //     "selectedColor": "#71A195",
    //    "color": "#094130",
    //    "selectedColor": "#3E3F41",
        // "color":"#9D9D9C",
        "outlineColor": "#D9D9D9",
        "outlineThickness": 2,
        // "rollOverOutlineColor": "#071C35",
        "rollOverOutlineColor": "#D9D9D9",
        "addClassNames": true
    },
    "export": {
        "enabled": false
    },

    "listeners": [{
        "event": "clickMapObject",
        "method": function(event) {
            /*console.log("Clicked on: " + event.mapObject.title);
            console.log("Clicked on: " + event.mapObject.className);*/
            if (event.mapObject.className) {
                let city_heading = document.getElementById('city-name');
                city_heading.innerHTML = '<h3>' + event.mapObject.title + '</h3>';
                let loc_desc = document.getElementById('city-description');
                loc_desc.innerHTML = '<p>' + event.mapObject.locationDescription + '</p>';
                let loc_img = document.getElementById('city-img');
                loc_img.innerHTML = '<img src="' + event.mapObject.locationimageURL + '"/>';
                
            }
        }
    }]
});


var countrySelector = document.getElementById("countrySelector");

    countrySelector.addEventListener("change", function() {
        
        var selectedCountry = countrySelector.value;
       
        let country_desc = document.getElementById('country-desc');
        let country_heading = document.getElementById('country-title');
        let city_heading = document.getElementById('city-name');
        let loc_desc = document.getElementById('city-description');
        let loc_img = document.getElementById('city-img');
        country_heading.innerHTML = '';
        country_desc.innerHTML = '';
        city_heading.innerHTML = '';
        loc_desc.innerHTML = '';
        loc_img.innerHTML = '';
  
   <?php
       $args = array(
            'post_type' => 'map-locator',
            'posts_per_page' => -1, 
        );

        $map_locator_query = new WP_Query($args);
        $counter = 0;
        $if = "";
        if ($map_locator_query->have_posts()) {
            while ($map_locator_query->have_posts()) : $map_locator_query->the_post();
            if($counter == 0){
         $if = "if";
            }
            else{
               $if = "else if"; 
            }
                $country_name = get_field('country_name');
                $country_description = get_field('country_description');
                $longitude = get_field('longitude');
                $latitude = get_field('latitude');
                $pin_image = get_field('pin_image');
                $pin_class = get_field('pin_class');
              

               echo $if; ?>
            
               (selectedCountry == "<?php echo $pin_class; ?>") {
                    var lat = <?php echo $latitude; ?>;
                    var long = <?php echo $longitude; ?>;
                    var zoomlevel = 5;
                    var selectedClass = "selected-country";
                     
                    map.zoomToLongLat(zoomlevel, long, lat);
                    // areasSettings.selectedColor = "#71A195";
                    // areasSettings.color = "#71A195";
                    
                    var selectedObject = map.getObjectById(selectedCountry);
                    map.selectObject(selectedObject);
                    if (selectedObject) {
                        selectedObject.displayObject.node.classList.add(selectedClass);
                    }
                    
                    let country_heading = document.getElementById('country-title');
                    country_heading.innerHTML = '<h3><?php echo $country_name; ?></h3>';
                    let country_desc = document.getElementById('country-desc');
                    country_desc.innerHTML = '<p><?php echo $country_description; ?></p>';
              }
                <?php
                $counter++;
                 endwhile;
        }
            wp_reset_postdata(); 
              
  ?>
            else {
                map.goHome();
        
            }

});


        document.addEventListener('DOMContentLoaded', function () {
            var countrySelector = document.getElementById('countrySelector');
            var optionPK = countrySelector.querySelector('option[value="PK"]');

            let country_heading = document.getElementById('country-title');
            country_heading.innerHTML = '<h3>PAKISTAN</h3>';
            let country_desc = document.getElementById('country-desc');
            country_desc.innerHTML = '<p>More than 200 offices <br> and 2000+ employees</p>';
            
            if (optionPK) {
                optionPK.selected = true;
            }
        });
        window.addEventListener("load", (event) => {
            var lat = 30.644680862897165;
            var long = 71.68337471353064;
            var zoomlevel = 10;
            var selectCountry = "PK";
            var selectedClass = "selected-country";
            map.zoomToLongLat(zoomlevel, long, lat);
            var selectedObject = map.getObjectById(selectCountry);
                map.selectObject(selectedObject);
                if (selectedObject) {
                    selectedObject.displayObject.node.classList.add(selectedClass);
                }
        });


// for showing markers after zoomin
map.addListener( "rendered", function() {
  revealMapImages();
  map.addListener( "zoomCompleted", revealMapImages );
} );
function revealMapImages( event ) {
  var zoomLevel = map.zoomLevel();
  if ( zoomLevel < 2 ) {
    map.hideGroup( "minZoom-2" );
    map.hideGroup( "minZoom-2.5" );
  } else if ( zoomLevel < 2.5 ) {
    map.showGroup( "minZoom-2" );
    map.hideGroup( "minZoom-2.5" );
  } else {
    map.showGroup( "minZoom-2" );
    map.showGroup( "minZoom-2.5" );
  }
}

// jQuery(document).ready(function($){
//   jQuery('#countrySelector').find('option[value=PK]').attr('selected','selected');
// });

</script>
<style>
/*body {*/
/*    background: #0D4C39;*/
/*}*/

#chartdiv {
    width: 100%;
    height: 700px;
}

.chardiv-content {
    position: absolute;
    top: 20%;
    left: 10%;
    margin: 0 0 0 15px;
}

.chardiv-content h2,
.chardiv-content h3,
.chardiv-content p {
    color: #3E3F41 !important;
    font-weight: 300;
}

/*section#hb-page {*/
/*    background: #0D4C39;*/
/*}*/

/*section#hb-page .container{*/
/*    width: 1140px;*/
/*}*/
.imarat_map_container {
   /* background-color:#0D4C39;*/
    background-color:rgba(238, 238, 238, 1);
}

#chartdiv a {
    display: none !important;
}

select#countrySelector {
    /* background: rgb(255 255 255 / 10%); */
    /* background: rgb(255 255 255); */
    color: #3E3F41;
    margin: 20px 0;
    padding-left: 30px;
    border-radius: 2px;
    border: 1px solid rgb(147 147 147 / 60%);
}

.icon-select {
    position: relative;
}

.icon-select i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    margin: 0 10px;
    color: #3E3F41;
}

#country-desc p {
    font-size: 16px;
    font-weight: 700;
    line-height: 16px;
    margin: 10px 0;
}

#country-title h3 {
    font-size: 30px;
    font-weight: 700;
}

.loc-content {
    width: 200px;
    max-width:200px;
    display: flex;
    flex-direction: column;
    text-align: center;
    /* background: rgb(246 246 246 / 20%); */
    background: rgb(255 255 255 / 80%);
   
    border-radius: 12px;
}

.loc-content img {
    width: 100%;
    max-height: 91px;
    object-fit: cover;
    border-radius: 12px 12px 0 0;
}
.loc-content h3 {
    font-size: 14.78px;
    font-weight: 700;
       margin: 10px 0 3px;
}
.loc-content p {
    font-size: 12px;
    font-weight: 400;
    line-height: 16px;
    margin-bottom: 10px;
    padding: 5px 10px;
}

.selected-country{
   /* fill: #71A195;*/
    fill: #9D9D9C;
}
</style>
<?php
 return ob_get_clean();
}
