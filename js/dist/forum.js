module.exports=function(t){var e={};function o(n){if(e[n])return e[n].exports;var r=e[n]={i:n,l:!1,exports:{}};return t[n].call(r.exports,r,r.exports,o),r.l=!0,r.exports}return o.m=t,o.c=e,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)o.d(n,r,function(e){return t[e]}.bind(null,r));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="",o(o.s=18)}([function(t,e){t.exports=flarum.core.compat.app},function(t,e,o){"use strict";function n(t,e){t.prototype=Object.create(e.prototype),t.prototype.constructor=t,t.__proto__=e}o.d(e,"a",(function(){return n}))},function(t,e){t.exports=flarum.core.compat.Model},,,function(t,e){t.exports=flarum.core.compat.extend},function(t,e){t.exports=flarum.core.compat["components/TextEditor"]},,function(t,e){t.exports=flarum.core.compat["utils/mixin"]},function(t,e){t.exports=flarum.core.compat["common/Component"]},function(t,e){t.exports=flarum.core.compat["common/components/Button"]},function(t,e){t.exports=flarum.core.compat["common/components/LoadingIndicator"]},function(t,e){t.exports=flarum.core.compat["common/utils/classList"]},,,,,,function(t,e,o){"use strict";o.r(e);var n=o(0),r=o.n(n),i=o(1),a=o(2),s=o.n(a),u=o(8);var l=function(t){function e(){return t.apply(this,arguments)||this}return Object(i.a)(e,t),e.prototype.bbcode=function(){return function(t){switch(t.type()){case"file":return"["+t.name()+"]("+t.url()+")";case"image":return"[IMG]"+t.url()+"[/IMG]";case"audio":return"[AUDIO]"+t.url()+"[/AUDIO]";case"video":return"[VIDEO]"+t.url()+"[/VIDEO]";default:return"["+t.name()+"]("+t.url()+")"}}(this)},e}(o.n(u)()(s.a,{url:s.a.attribute("url"),name:s.a.attribute("name"),uuid:s.a.attribute("sha"),type:s.a.attribute("type"),created_at:s.a.attribute("created_at"),path:s.a.attribute("path")})),p=o(5),d=o(6),c=o.n(d),f=o(9),h=o.n(f),g=o(10),v=o.n(g),b=o(11),y=o.n(b),E=o(12),x=o.n(E),O=function(t){function e(){return t.apply(this,arguments)||this}Object(i.a)(e,t);var o=e.prototype;return o.oninit=function(e){var o=this;t.prototype.oninit.call(this,e),this.attrs.uploader.on("uploaded",(function(){o.$("form")[0].reset(),m.redraw()}))},o.oncreate=function(e){t.prototype.oncreate.call(this,e),this.isMediaUploadButton||this.$().tooltip()},o.view=function(){var t=this.attrs.uploader.uploading?r.a.translator.trans("flarum-ext-github-upload.forum.states.loading"):r.a.translator.trans("flarum-ext-github-upload.forum.buttons.attach"),e=!this.isMediaUploadButton&&t||" ";return m(v.a,{className:x()(["Button","hasIcon","irony-github-upload-button",!this.isMediaUploadButton&&!this.attrs.uploader.uploading&&"Button--icon",!this.isMediaUploadButton&&!this.attrs.uploader.uploading&&"Button--link",this.attrs.uploader.uploading&&"uploading"]),icon:!this.attrs.uploader.uploading&&"fas fa-cloud-upload-alt",onclick:this.uploadButtonClicked.bind(this),title:e,disabled:this.attrs.disabled},this.attrs.uploader.uploading&&m(y.a,{size:"tiny",className:"LoadingIndicator--inline Button-icon"}),(this.isMediaUploadButton||this.attrs.uploader.uploading)&&m("span",{className:"Button-label"},t),m("form",null,m("input",{type:"file",multiple:!0,onchange:this.process.bind(this)})))},o.process=function(t){var e=this.$("input").prop("files");0!==e.length&&this.attrs.uploader.upload(e,!this.isMediaUploadButton)},o.uploadButtonClicked=function(t){this.$("input").click()},e}(h.a),B=function(){function t(t,e){this.upload=t,this.composerElement=e,this.handlers={},this.supportsFileDragging()&&(this.composerElement.addEventListener("dragover",this.handlers.in=this.in.bind(this)),this.composerElement.addEventListener("dragleave",this.handlers.out=this.out.bind(this)),this.composerElement.addEventListener("dragend",this.handlers.out),this.composerElement.addEventListener("drop",this.handlers.dropping=this.dropping.bind(this)))}var e=t.prototype;return e.supportsFileDragging=function(){var t=document.createElement("div");return("draggable"in t||"ondragstart"in t&&"ondrop"in t)&&"FormData"in window&&"FileReader"in window},e.unload=function(){this.handlers.in&&(this.composerElement.removeEventListener("dragover",this.handlers.in),this.composerElement.removeEventListener("dragleave",this.handlers.out),this.composerElement.removeEventListener("dragend",this.handlers.out),this.composerElement.removeEventListener("drop",this.handlers.dropping))},e.isNotFile=function(t){if(t.dataTransfer.items)for(var e=0;e<t.dataTransfer.items.length;e++)if("file"!==t.dataTransfer.items[e].kind)return!0;return!1},e.in=function(t){this.isNotFile(t)||(t.preventDefault(),this.over||(this.composerElement.classList.add("fof-upload-dragging"),this.over=!0))},e.out=function(t){this.isNotFile(t)||(t.preventDefault(),this.over&&(this.composerElement.classList.remove("fof-upload-dragging"),this.over=!1))},e.dropping=function(t){this.isNotFile(t)||(t.preventDefault(),this.upload(t.dataTransfer.files),this.composerElement.classList.remove("fof-upload-dragging"))},t}(),w=function(){function t(){this.callbacks={success:[],failure:[],uploading:[],uploaded:[]},this.uploading=!1}var e=t.prototype;return e.on=function(t,e){this.callbacks[t].push(e)},e.dispatch=function(t,e){this.callbacks[t].forEach((function(t){return t(e)}))},e.upload=function(t,e){var o=this;void 0===e&&(e=!0),this.uploading=!0,this.dispatch("uploading",t),m.redraw();for(var n=new FormData,r=0;r<t.length;r++)n.append("files[]",t[r]);return app.request({method:"POST",url:app.forum.attribute("apiUrl")+"/irony/github/upload",serialize:function(t){return t},body:n}).then((function(t){return o.uploaded(t,e)})).catch((function(t){throw o.uploading=!1,m.redraw(),t}))},e.uploaded=function(t,e){var o=this;void 0===e&&(e=!1),this.uploading=!1,t.data.forEach((function(t){var n=app.store.pushObject(t);o.dispatch("success",{file:n,addBBcode:e})})),this.dispatch("uploaded")},t}(),j={DragAndDrop:B,Uploader:w};r.a.initializers.add("irony-github-upload",(function(t){Object(p.extend)(c.a.prototype,"oninit",(function(){this.uploader=new w})),Object(p.extend)(c.a.prototype,"controlItems",(function(t){r.a.forum.attribute("canUploadToGithub")&&t.add("irony-github-upload",O.component({uploader:this.uploader}))})),Object(p.extend)(c.a.prototype,"oncreate",(function(t,e){var o=this;if(r.a.forum.attribute("canUploadToGithub")){this.uploader.on("success",(function(t){var e=t.file,n=t.addBBcode;if(console.log(e),n&&e.url()&&(o.attrs.composer.editor.insertAtCursor(e.bbcode()+"\n"),"function"==typeof o.attrs.preview)){var i=r.a.composer.isFullScreen;r.a.composer.isFullScreen=function(){return!1},o.attrs.preview(),r.a.composer.isFullScreen=i}}));var n=new B((function(t){return o.uploader.upload(t)}),this.$().parents(".Composer")[0]);this.$("textarea").bind("onunload",(function(){n.unload()}))}})),t.store.models.files=l})),o.d(e,"components",(function(){return j}))}]);
//# sourceMappingURL=forum.js.map