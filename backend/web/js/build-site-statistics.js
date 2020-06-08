
   $("#search").on("click",function(){
            $("#buildSiteForm").attr("action","index");
            $("#buildSiteForm").submit();
        })
    $("#export").click(function(){
        $("#buildSiteForm").attr("action","export");
        $("#buildSiteForm").submit();
    });
