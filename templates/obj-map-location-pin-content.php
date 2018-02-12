<div class="obj-google-maps-infowindow" itemscope itemtype="http://schema.org/LocalBusiness">
  <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
    <meta itemprop="latitude" content="<?php echo $obj_location_lat; ?>" />
    <meta itemprop="longitude" content="<?php echo $obj_location_lng; ?>" />
  </div>
  <p itemprop="name"><strong><?php echo $obj_location_post_title; ?></strong></p>
  <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
    <span class="street-address" itemprop="streetAddress"><?php echo "$obj_location_street_number $obj_location_route"; ?><?php echo !empty( $obj_location_subpremise ) ? " #$obj_location_subpremise" : ''; ?></span><br />
    <span class="city-name" itemprop="addressLocality"><?php echo $obj_location_locality; ?></span
      >, <span class="state-name" itemprop="addressRegion"><?php echo $obj_location_administrative_area_level_1; ?></span
      > <span class="country-name" itemprop="addressCountry"><?php echo $obj_location_country; ?></span
      > <span class="zip" itemprop="postalCode"><?php echo $obj_location_postal_code; ?></span>
  </p>
  <p><a href="<?php echo $obj_location_permalink; ?>" itemprop="url">View <?php echo $obj_location_post_type_label; ?></a></p>
</div>