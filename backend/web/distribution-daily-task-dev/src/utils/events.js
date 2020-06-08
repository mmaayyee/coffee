/* eslint-disable */
export default {
    el:document,
    addEvent(type, fn, capture) {
        var el = this.el;
        if (window.addEventListener) {
            el.addEventListener(type, fn, capture);
            var ev = document.createEvent("HTMLEvents");
            ev.initEvent(type, capture || false, false);
            if (!el["ev" + type]) {
                el["ev" + type] = ev;
            }
        } else {
            alert("请使用chrome等浏览器")
        }
        return this;
    },
    setData(type,dataname,val) {
        var el = this.el;
        if (window.removeEventListener) {
            if (typeof type === "string" && typeof dataname === "string") {
                el["ev" + type][dataname] = val;
            }
        } else {
            alert("请使用chrome等浏览器")
        }
    },
    emitEvent: function(type) {
        var el = this.el;
        if (typeof type === "string") {
            if (document.dispatchEvent) {
                if (el["ev" + type]) {
                    el.dispatchEvent(el["ev" + type]);
                }
            } else {
                alert("请使用chrome等浏览器")
            }
        }
        return this;
    },
    removeEvent: function(type, fn, capture) {
        var el = this.el;
        if (window.removeEventListener) {
            el.removeEventListener(type, fn, capture || false);
        } else {
            alert("请使用chrome等浏览器")
        }
        return this;
    }
}
