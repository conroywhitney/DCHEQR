$("#projSubmit").click(function() {
    var DC_URL = $("#projURL").val();
    // TODO: check that given URL matches what we expect in a DonorsChoose URL
    //if (DC_URL.substring(0, 52) == 'http://www.donorschoose.org/donors/proposal.html?id=') {

    var proj_id = DC_URL.substr(52, 8);
    var encoded_url = encodeURIComponent(DC_URL);

    $.ajax({
        url: "http://api.donorschoose.org/common/json_feed.html",
        type: "get",
        data: {
            'solrQuery': "id:" + proj_id,
            'APIKey': 'DONORSCHOOSE'
        },
        dataType: "jsonp",
        cache: false,
        success: displayProject
    });

    $.get("http://api.bitly.com/v3/shorten?login=dcheqr&apiKey=R_d5fcb40ad8e5264d79035db8deb79107&format=txt&longUrl=" + encoded_url, displayQR);
});

function displayQR(response) {
    // TODO: check that bitly response matches what we would expect
    var bitly_url = jQuery.trim(response) + ".qrcode";
    $("#projQR").attr("src", bitly_url);

    // Upload to CafePress
    var qr_code = bitly_url.substr(14);
    //console.log("qr_code [" + qr_code + "]");

    $.get("/upload/qr/" + qr_code, displaySchwag);
}

function displayProject(response) {
    // TODO: check that response is correct
    // TODO: check that has proposal
    //console.log(response);
    var project = response.proposals[0];
    if (project) {
        $("#projImage").attr("src", project.imageURL);
        $("#projTitle").html(project.title);
        $("#projSchool").html(project.schoolName);
        $("#projDesc").html(project.shortDescription);
        $("#projCity").html(project.city);
        $("#projState").html(project.state);
    }
}

function displaySchwag(response) {
    //console.log(response);
    var product = jQuery.parseJSON(response);
    $("#prodImage").attr("src", product.image);
    $("#prodName").html(product.name);
    $("#prodPrice").html(product.price);
    $("#prodURL").attr("href", product.url);
}