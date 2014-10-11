//Called on body load to start the blinking task and also key events.
function startup() {
    var e = false;
    var t = false;
    i = true;
    window.setInterval(function() {
        if (e) return;
        if (i == true) {
            tempHtml = $("#Output").text();
            if (endsWith(tempHtml, "\n")) {
                $("#Output").text(tempHtml + " __")
            } else {
                $("#Output").text(tempHtml + "__")
            }
        } else {
            tempHtml = $("#Output").text();
            $("#Output").text(tempHtml.replace("__", ""))
        }
        i = !i
    }, 700);
    $(document).keydown(function(n) {
        if (t) return false;
        if ($(n.target).is("input")) {
            setTimeout(function() {
                e = false
            }, 400);
            return
        }
        if (n.ctrlKey) {
            t = true;
            return
        }
        e = true;
        tempHtml = $("#Output").text();
        $("#Output").text(tempHtml.replace("__", ""));
        tempHtml = $("#Output").text();
        $("#Output").text(tempHtml.replace("__", ""));
        $("#Output").text(tempHtml + String.fromCharCode(n.keyCode));
        tempHtml = $("#Output").text();
        setTimeout(function() {
            e = false
        }, 400);
        return false
    });
    $(document).keydown(function(e) {
        if (e.ctrlKey) {
            t = false;
            return
        }
    })
}

//Handles the insertion.
function insert(e) {
    if (e == "") {
        e = randomGen()
    }
    loadXMLDoc("?INSERT=" + e)
}

//Handles getting the ID.
function get_id(e) {
    if (e == "") {
        min = 1;
        max = 15;
        e = Math.floor(Math.random() * (max - min + 1)) + min
    }
    loadXMLDoc("?GET_ID=" + e)
}

//Handles dumping the data.
function dump_data() {
    loadXMLDoc("?SQL_DATA")
}

/*
 * Once handled by it's function it's passed to the AJAX MAKER!
 * WOOOOO AJAX. <3 
 */
function loadXMLDoc(e) {
    var t;
    if (window.XMLHttpRequest) {
        t = new XMLHttpRequest
    } else {
        t = new ActiveXObject("Microsoft.XMLHTTP")
    }
    t.onreadystatechange = function() {
        if (t.readyState == 4 && t.status == 200) {
            $("#OutputContainer").show();
            $("#Output").text(t.responseText)
        }
    };
    t.open("GET", window.location.href.split("?")[0] + e, true);
    t.send()
}

/*
 * Just to tell if a string ends in a string.
 */
function endsWith(e, t) {
    return e.indexOf(t, e.length - t.length) !== -1
}

/*
 * You see that help button. 
 * Yes. This inserts the malicious code.
 */
function insertAssist() {
    $("#IDNumber").val("1';TRUNCATE TABLE `Hack_Data`;'")
}

/*
 * If you're too lazy to keyboard slam then call this!
 */
function randomGen() {
    var e = "";
    var t = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (var n = 0; n < 7; n++) e += t.charAt(Math.floor(Math.random() * t.length));
    return e
}
