const searchInput = document.getElementById("search-input");
const searchButton = document.getElementById("search-button");

searchInput.addEventListener("keyup", function (event) {
  if (event.keyCode === 13) {
    searchButton.click();
  }
});

searchButton.addEventListener("click", function () {
  var searchValue = searchInput.value;
  axios
    .get(
      "https://nominatim.openstreetmap.org/search?q=" +
        searchValue +
        "&format=json&limit=1"
    )
    .then(function (response) {
      var result = response.data[0];
      map.setView([result.lat, result.lon], 17);
    })
    .catch(function (error) {});
});
