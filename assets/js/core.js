$(document).ready(function() {
    $("#signature").jSignature({
        // line color
        color: "#f00",

        // line width
        lineWidth: 3,
    });

    let $sigdiv = $("#signature");
    let datapair = $sigdiv.jSignature("getData", "svgbase64");

    $("#signature").bind("change", function(e) {
        let data = $("#signature").jSignature("getData");
        $("#client_signature").val(data);
    });

    $("#reset").click(function(e){
        $("#signature").jSignature("clear");
        let data = $("#signature").jSignature("getData");
        $("#client_signature").val("");
        e.preventDefault();
    });

    $("#submit").click(function(e) {

        $("#signature_form").slideUp(200);
        //$(".buttons").slideUp(300);
        $("#signature_form").after("<img id=\"hk\" class=\"hidden\" />");
        let data = $("#signature").jSignature("getData");
        $("#hk").attr("src", data );
        $("#hk").slideDown(200);
        // Loading text
        $("#dev_signature").css("opacity",".625")
        $("#content").css({"color":"#aaa","list-style-color":"#aaa !important"}).append("<div id=\"loading_area\"></div>");
        $("#loading_area").append("<h2 id=\"loading\" style=\"text-align:center; color:green; display:none;\">Saving Contract…</h2>");
        $("#loading_area").append("<p id=\"loading2\" style=\"text-align:center; color:#222; display:none;\">This shouldn\'t take more than a minute.</p>");

        $("#loading").slideDown(300, function() {
            $("#loading2").delay(2000).slideDown(300);
        });
    });
});