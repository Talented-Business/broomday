jQuery(document).ready(function()
{
	
	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osmAttrib='&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
	var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib});
	var map = new L.Map('map-container').addLayer(osm).setView([51.505, -0.09], wccm_map.zoom_level);
	
	//geocoding
	var geocode = 'https://www.mapquestapi.com/geocoding/v1/address?key=bH5hQRTjHiWVRJErm6OtLiL9SxsXiOPr&location=' + wccm_map.address;

	// use jQuery to call the API and get the JSON results
	jQuery.getJSON(geocode, function(data) 
	{
		if(data.results[0].locations.length > 0)
		{
			//console.log(data.results[0].locations[0].latLng.lat);
		
			map.setView([data.results[0].locations[0].latLng.lat, data.results[0].locations[0].latLng.lng], wccm_map.zoom_level);
			var marker = L.marker([data.results[0].locations[0].latLng.lat, data.results[0].locations[0].latLng.lng]).addTo(map);
		}
	});
});

function wccm_map_on_geocoding_completed(result)
{
	console.log(results);
}