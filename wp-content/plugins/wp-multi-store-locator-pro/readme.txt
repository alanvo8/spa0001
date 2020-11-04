=== WP Multi Store Locator Pro ===
Contributors: wpexpertsio
Requires at least: 4.5.0
Tested up to: 5.4.1
Stable tag: 4.2
Tags: Store Locator, Search Store , Store Categories, Store Shortcode, Store Sales Manager,geocoding,  Import/Export Store ,Search stats ,
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

== Description ==
Admin highlights :
1- Can manage Stores.
2-  Can  manage Sale managers of their added Stores.
3-  Can  manage Store Categories.
4-  Can  manage Store Tags.
5- User can add shortcode to show Store Locator.
6- user can  manage Map Settings like Show map on frontend,Map Width,Map Height,Map Type,Search Unit,Search radius options,Enable Search 
with categories,Enable Search with Tags,Show street view control,Show map type control,Zoom by scroll on map,Marker Clusterer,Cluster Size,User Marker, and more…..

Import/Export :

7-  User can import/export Stores,Sales manager.

Stores Statistics :

8- User can see overall Total number of searchs and by with Store name.



Frontend highlights :

1-  User can search Store from their current location (SSL must be active ) and with desired location.

2- User can search Stores by selected units.

3- User can search by Selected Store Categories.

== Installation ==

https://codex.wordpress.org/Managing_Plugins#Installing_Plugins


== Changelog ==

== 1.0 == 

initial release.


= 1.1 - 30/05/2017 =

Added - attribute "Location" in shortcode. Added attribute "City" in shortcode. Added attribute "State" in shortcode. 
Added - attribute "Radius" in shortcode. 
Added - Option to display default map with in specific radius/location. [store_locator_show city="Alabama" state="US" radius="25"] 
Fixed - Direction Icon.
Added - New Layouts
Added - Store Listings
Added - Counter icon
Added - Custom Map Styling
Added - Option to Enable/ Disable Search Filter
Added - Search Placeholder Option
Added - Add Your Own Marker
Added - Search Toggle Disable / Enable Option

= 1.1.1 - 14/06/2017 =

Fixed - broken icon in listing
Added - Optimized Code
Added - Optimized JS

= 1.2 - 07/07/2017 =

Added - RTL Support
Added - Add Support For WPML
Added - Add Support For Avada (Fusion Builder)
Added - Add Support For Divi Builder
Added - Add Support For Visual Composer.

= 1.2.1 - 24/07/2017 =
Fixed - Js store_locatore_search_lat,store_locatore_search_lng remove from hidden field to get direction.

= 1.2.2 - 28/07/2017 =
Added - Single Page Functionality For Your Stores.

= 1.3 - 21/08/2017 =

Fixed - crose browser Css fixes.
Fixed - Map Height Setting.
Fixed - Listing Setting.
Fixed - WooCommerce Confliction.
Added - Support For Divi builder.
Added - Support For Elementor builder.
Added - Support For Beaver builder.
Added - Support For UPD Power Builder ( Cherry framework ).
Update - Overhaul the of import functionality.

= 1.3.1 - 08/09/2017 =

Added - Compatible with Jupiter Version: 5.9.7
Fiexd - Responsive CSS on search store layout.

= 1.4 - 19/09/2017 =

Added - Translation support using .PO .MO files

= 1.5 - 11/10/2017 =

Fixed - Statistics Page Fixes.
Fixed - Store import instantly.
Added - Confliction with niceSelect removed.
Added - Clickable Call button on store phone number infowindow.

= 1.8 - 18/1/2018 =

Added - Store List Dynamic Label. 
Added - Support for import/export store categories. 
Added - Optimized Import/Export Store compatibility.

= 2.4 - 15/02/2018 =

Added - Introduced map clusters functionality
Added - Import/export categories.
Added - Default location.
Added - Map Zoom by scroll dynamic setting.
Added - Added new layout.
Added - Added multiple layout options.
Fixed - Search locations.
fixed - Admin settings in a easy way.
fixed - CSS related issues.

= 2.6 - 07/03/2018 =

Fixed - Get my location SSL check.
Added - Search location with function based.
Fixed - Map Search Open as Default with back end setting.

= 2.7 - 30/03/2018 =

Fixed - Search box toggle issue.

= 2.8 - 12/06/2018 =

Fixed - Snazzy Maps styles issue.
Fixed - Infobox store featured image issue.
Added - Number of markers to be displayed on map.
Added - Visit website label to be dyanmic.

= 2.9 - 01/17/2019 =

Compatible upto WordPress 5.0.3
Fixed - Admin Backend CSS issue fixed
Fixed - Fixed minor php bugs

= 3.0.0 - 06/09/2019 =
Added - Multiple Maps By Categories.
Added - Info Window Customization.
Added - Google Maps Detailed Direction On Map & Redirect.
Added - Custom Markers.
Added - Category Based Markers
Added - Embed Map Functionality
Added - Multiple Templates Support For Maps

= 3.0.1 - 28/10/2019 =

Fixed - Old shortcode CSS issues fixed


= 3.5.0 - 13/1/2020 =

Fixed - Sales Managers not saving.
Fixed - Use category markers for store markers  not working.
Fixed - Different Map Styles Not working.
Fixed - Get Directions showing directions in terms of 'walking' instead of 'driving'.
Added - 'clear stores' button on import.
Added - Longitude and Latitude in import and export.
Fixed - Shortcode showing custom and fullscreen map should now be compitable with gutenberg and divi theme.
Fixed - Stores show up in frontend, even after they are deleted from trash in wordpress admin.

= 4.0 - 18/2/2020 =

Added - Freemius Integration
Added - 'Fit Screen To Stores' Option.
Added - 'Fill Radius with color' Option.
Added - 'Fill color' Option.
Added - Export stores by category option.
Added - Reset map option in search.
Added 'Reset to Default' option in Global Map Settings.
Improvement - Stores now showing from closest to the farthest in grid.
Improvement - in custom maps on clicking a store info-window the address will now populate in directions route end field
Improvement - displayed a message when map is disabled from back-end
Fixed - 'Default Map Zoom Level' was not being applied.
Fixed - 'Location Search Zoom Level' was not being applied.
Fixed - Category Image was not saving on update in category page.
Fixed - Showing all categories in search options instead of the ones assigned to map.
Fixed - 'Maximum number of markers to be displayed' not working
Fixed - after import, stores not visible on map until we update each store.
Fixed - Map not rendering in Elementor page editor.
Fixed - All visible warnings and notices.
Removed - 'Location not found text' option from admin options

= 4.1 - 30/04/2020 =

Added - Option to add category and description in store info-window in custom map.
Added - Option to import store description via csv.
Fixed - Google Logo Hide Issue Fixed

= 4.2 - 29/06/2020 =

Fixed - Some part of map is behind stores grid.
Fixed - Stores sorting by distance not working when we search a location.
Fixed - Only city name showing up in location field in full screen map
Fixed - On Get Direction it always shows direction from default location to store location.
Fixed - Notices and warnings.
Fixed - Multi Store Locator element in WPBakery Page Builder not working.
Fixed - '+' sign being added if phone number column is empty in Store Import.
Added - Email and Description in Store Export￼.
Added - Option to hide 'Points of Interest'.