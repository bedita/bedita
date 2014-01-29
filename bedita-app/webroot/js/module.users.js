var urlAddObjToAssoc = BEDITA.base + 'pages/loadObjectToAssoc';

function addObjToAssoc(url, postdata) {
    //postdata.tplname = 'pippi';
    $("#loadingDownloadRel").show();
    $.post(url, postdata, function(html){
        $("#loadingDownloadRel").hide();
        // add row
        $("table.group_objects").append(html);
    });
}
