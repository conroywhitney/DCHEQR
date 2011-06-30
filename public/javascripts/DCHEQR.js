$("#txtProjURL").change(function() {
    var DC_URL = $(this).val();

    // TODO: check that given URL matches what we expect in a DonorsChoose URL
    //if (val.substring(0, 52) == 'http://www.donorschoose.org/donors/proposal.html?id=') {
    DC_URL = encodeURIComponent(DC_URL);

    $.get("http://api.bitly.com/v3/shorten?login=dcheqr&apiKey=R_d5fcb40ad8e5264d79035db8deb79107&format=txt&longUrl=" + DC_URL, displayQR);
});

function displayQR(response) {
    // TODO: check that bitly response matches what we would expect
    $("#imgProjQR").attr("src", response + ".qrcode");
}