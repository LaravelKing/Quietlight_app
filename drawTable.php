<!DOCTYPE html>
<html lang="en">
<head>
    <title>CSV Uploading & Show Table</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
        integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
        crossorigin="anonymous">
</head>
<body>
    <div style="margin-top: 40px;">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Please Select CSV File</h2>
            </div>
        </div>

        <div class="row">
            <form method="post" id="frmMain" enctype="multipart/form-data" action="async-uploadCsv.php">
                <input type="hidden" name="upload" value="1">
                <div class="col-sm-6 col-sm-offset-3">
                    <input type="file" name="file" class="form-control" id="file"/>
                </div>
            </form>
        </div>

        <div id="js-div-draw" class="hide">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3" style="margin-top: 10px;">
                    <select class="form-control" id="js-select-column">
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-sm-offset-3" style="margin-top: 10px;">
                    <button class="btn btn-primary btn-lg btn-block" id="js-btn-draw">
                        Draw Table
                    </button>
                </div>
            </div>
        </div>

        <div id="js-div-data-area" class="hide" style="margin-bottom: 30px;">
            <div class="row">
                <div class="col-sm-12 text-center" style="margin-top: 50px;">
                    <h2>Show All Origin Data</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="margin-top: 10px;">
                    <table class="table" id="js-table-data" style="font-size: 11px;"></table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-center" style="margin-top: 30px;">
                    <h2>Show All Data Yearly</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="margin-top: 10px;">
                    <table class="table" id="js-table-data-year" style="font-size: 11px;"></table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-center" style="margin-top: 30px;">
                    <h2>Show All Data Monthly</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="margin-top: 10px;">
                    <table class="table" id="js-table-data-month" style="font-size: 11px;"></table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 text-center" style="margin-top: 30px;">
                    <h2>Show All Specify Category Data</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="margin-top: 10px;">
                    <table class="table" id="js-table-data-specify" style="font-size: 11px;"></table>
                </div>
            </div>
        </div>
    </div>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <script>
        var filename = '';
        var dataRaw, dataYear = [], arrYear = [], dataMonth = [], arrMonth = [];
        var columnName = [], monthName = [];
        var dataMain;
        var is_found = false;
        $(document).ready(function() {
            $("input#file").change( function(){
                $(this).parents("form").ajaxForm({
                    success : fnUploaded
                }).submit();
            });

            $("button#js-btn-draw").click(function() {

                // draw all data
                var strHTML = "<tr><td></td>";
                for (var i = 0; i < monthName.length; i++) {
                    if (/\d/.test(monthName[i])) {
                        strHTML += "<th class='text-center'>" + monthName[i] + "</th>";
                    }
                }
                strHTML += "</tr>";

                $.each(dataRaw , function (key, value) {
                    if (key == '') {
                        return;
                    }
                    strHTML += "<tr>";
                    strHTML += "<th>" + key + "</th>";
                    $.each(value , function (key2, value2) {
                        if (/\d/.test(key2)) {
                            strHTML += "<td class='text-right'>" + value2 + "</td>";
                        }
                    });
                    strHTML += "</tr>";
                });
                $("#js-table-data").html(strHTML);

                // draw data yearly
                $.each(dataRaw , function (key, value) {
                    if (key == '') {
                        return;
                    }
                    var itemYear = [];
                    $.each(value , function (key2, value2) {
                        if (/\d/.test(key2)) {
                            var year = key2.substring(3, 5);
                            arrYear[arrYear.length] = year;
                            if (!itemYear.hasOwnProperty(year)) {
                                itemYear[year] = 0;
                            }
                            itemYear[year] = itemYear[year] + (value2 + "").replace(',', '') * 1;
                        }
                    });
                    dataYear[dataYear.length] = {key, itemYear};
                });

                var uniqueVals = [];
                $.each(arrYear, function(i, el){
                    if($.inArray(el, uniqueVals) === -1) uniqueVals.push(el);
                });
                arrYear = uniqueVals;

                strHTML = "<tr><td></td>";
                for (var i = 0; i < arrYear.length; i++) {
                    strHTML += "<th class='text-center'>20" + arrYear[i] + "</th>";
                }
                strHTML += "</tr>";

                $.each(dataYear , function (key, value) {
                    strHTML += "<tr>";
                    strHTML += "<th>" + value['key'] + "</th>";
                    for (var i = 0; i < arrYear.length; i++) {
                        strHTML += "<td class='text-center' width='35%'>" + Math.round(value['itemYear'][arrYear[i]], 2) + "</td>";
                    }
                    strHTML += "</tr>";
                });
                $("#js-table-data-year").html(strHTML);

                // draw data monthly
                $.each(dataRaw , function (key, value) {
                    if (key == '') {
                        return;
                    }
                    var itemMonth = [];
                    $.each(value , function (key2, value2) {
                        if (/\d/.test(key2)) {
                            var month = key2.substring(0, 2);
                            arrMonth[arrMonth.length] = month;
                            if (!itemMonth.hasOwnProperty(month)) {
                                itemMonth[month] = 0;
                            }
                            itemMonth[month] = itemMonth[month] + (value2 + "").replace(',', '') * 1;
                        }
                    });
                    dataMonth[dataMonth.length] = {key, itemMonth};
                });

                var uniqueVals = [];
                $.each(arrMonth, function(i, el){
                    if($.inArray(el, uniqueVals) === -1) uniqueVals.push(el);
                });
                arrMonth = uniqueVals;
                arrMonth.sort();

                strHTML = "<tr><td></td>";
                var arrMonthName = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                for (var i = 0; i < arrMonth.length; i++) {
                    strHTML += "<th class='text-center'>" + arrMonthName[i] + "</th>";
                }
                strHTML += "</tr>";

                $.each(dataMonth, function (key, value) {
                    strHTML += "<tr>";
                    strHTML += "<th>" + value['key'] + "</th>";
                    for (var i = 0; i < arrMonth.length; i++) {
                        strHTML += "<td class='text-center' width='7%'>" + Math.round(value['itemMonth'][arrMonth[i]], 2) + "</td>";
                    }
                    strHTML += "</tr>";
                });
                $("#js-table-data-month").html(strHTML);

                // draw data monthly & yearly for special category
                var columnName = $("select#js-select-column").val();
                var dataFocus = dataRaw[columnName];
                strHTML = "<tr><td></td>";
                var arrMonthName = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                for (var i = 0; i < arrMonth.length; i++) {
                    strHTML += "<th class='text-center'>" + arrMonthName[i] + "</th>";
                }
                strHTML += "</tr>";

                for (var i = 0; i < arrYear.length; i++) {
                    strHTML += "<tr>";
                    strHTML += "<th>20" + arrYear[i]+ "</th>";
                    for (var j = 0; j < arrMonth.length; j++) {
                        var timeline = arrMonth[j] + '-' + arrYear[i];
                        console.log(timeline);
                        if (dataFocus.hasOwnProperty(timeline)) {
                            strHTML += "<td class='text-center'>" + dataFocus[timeline]+ "</td>";
                        } else {
                            strHTML += "<td class='text-center'>0</td>";
                        }
                    }
                    strHTML += "</tr>";
                }

                $("#js-table-data-specify").html(strHTML);

                $("#js-div-data-area").removeClass('hide');

            });
        });

        function fnExtractData(data, columnName) {
            if (is_found == false) {
                $.each(data , function (key, value) {
                    if (is_found == false) {
                        if (key == columnName) {
                            is_found = true;
                            dataMain = value;
                            return;
                        } else if (value instanceof Object) {
                            fnExtractData(value, columnName);
                        }
                    }
                });
            }
        }

        function fnUploaded(data) {
            filename = data;
            $("div#js-div-draw").removeClass('hide');

            $.ajax({
                url: "async-getData.php",
                dataType : "json",
                type : "POST",
                data : {filename : filename},
                success : function(result) {
                    dataRaw = result.dataTable;
                    columnName = result.column;
                    monthName = result.month;
                    var strHTML = "";
                    for (var i = 0; i < columnName.length; i++) {
                        strHTML += "<option value='" + columnName[i] +"'>" + columnName[i] +"</option>";
                        $("select#js-select-column").html(strHTML);
                    }
                }
            });
        }
    </script>
</body>
</html>
