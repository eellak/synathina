var AjaxCall = (function(global) {

    function getXmlDoc() {
        var xmlDoc;
        if (window.XMLHttpRequest) {
            xmlDoc = new XMLHttpRequest();
        } else {
            xmlDoc = new ActiveXObject("Microsoft.XMLHTTP");
        }

        return xmlDoc;
    }

    function myGet(url, callback) {
        var xmlDoc = getXmlDoc();
        xmlDoc.open('GET', url, true);
        xmlDoc.onreadystatechange = function() {
            if (xmlDoc.readyState === 4 && xmlDoc.status === 200) {
                callback(xmlDoc);
            }
        }
        xmlDoc.send();
    }

    /** moved here as seemed more relative to this scope */
    function kmlToJson(func, kmlPath) {
        $.ajax(kmlPath).done(function(xml) {
            geoJSON = toGeoJSON.kml(xml);
            func(geoJSON)
        });
    }

    return {
        get : myGet,
        kmlToJson : kmlToJson
    }

})(window)

