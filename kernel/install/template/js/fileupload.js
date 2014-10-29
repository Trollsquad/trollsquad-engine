var fp = fp || {};

fp.extend = function(first, second){
    for (var prop in second){
        first[prop] = second[prop];
    }
};

fp.indexOf = function(arr, elt, from){
    if (arr.indexOf) return arr.indexOf(elt, from);

    from = from || 0;
    var len = arr.length;

    if (from < 0) from += len;

    for (; from < len; from++){
        if (from in arr && arr[from] === elt){
            return from;
        }
    }
    return -1;
};

fp.getUniqueId = (function(){
    var id = 0;
    return function(){ return id++; };
})();

fp.ie       = function(){ return navigator.userAgent.indexOf('MSIE') != -1; };
fp.opera    = function(){ return navigator.userAgent.indexOf('Opera') != -1; };
fp.safari   = function(){ return navigator.vendor != undefined && navigator.vendor.indexOf("Apple") != -1; };
fp.chrome   = function(){ return navigator.vendor != undefined && navigator.vendor.indexOf('Google') != -1; };
fp.firefox  = function(){ return (navigator.userAgent.indexOf('Mozilla') != -1 && navigator.vendor != undefined && navigator.vendor == ''); };
fp.windows  = function(){ return navigator.platform == "Win32"; };

//
// Events

fp.attach = function(element, type, fn){
    if (element.addEventListener){
        element.addEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.attachEvent('on' + type, fn);
    }
    return function() {
      fp.detach(element, type, fn)
    }
};
fp.detach = function(element, type, fn){
    if (element.removeEventListener){
        element.removeEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.detachEvent('on' + type, fn);
    }
};

fp.preventDefault = function(e){
    if (e.preventDefault){
        e.preventDefault();
    } else{
        e.returnValue = false;
    }
};

fp.insertBefore = function(a, b){
    b.parentNode.insertBefore(a, b);
};
fp.remove = function(element){
    element.parentNode.removeChild(element);
};

fp.contains = function(parent, descendant){
    if (parent == descendant) return true;

    if (parent.contains){
        return parent.contains(descendant);
    } else {
        return !!(descendant.compareDocumentPosition(parent) & 8);
    }
};

fp.toElement = (function(){
    var div = document.createElement('div');
    return function(html){
        div.innerHTML = html;
        var element = div.firstChild;
        div.removeChild(element);
        return element;
    };
})();

fp.css = function(element, styles){
    if (styles.opacity != null){
        if (typeof element.style.opacity != 'string' && typeof(element.filters) != 'undefined'){
            styles.filter = 'alpha(opacity=' + Math.round(100 * styles.opacity) + ')';
        }
    }
    fp.extend(element.style, styles);
};
fp.hasClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    return re.test(element.className);
};
fp.addClass = function(element, name){
    if (!fp.hasClass(element, name)){
        element.className += ' ' + name;
    }
};
fp.addImage = function(element,result) {
  $('.image_view[rel='+$(element).parents('.addImage').attr('rel')+']').remove();
  if ($('input[name='+$(element).parents('.addImage').attr('rel')+']').length > 0) $('input[name='+$(element).parents('.addImage').attr('rel')+']').val(result.id);
  else $(element).parents('.addImage').prepend('<input type="hidden" name="'+$(element).parents('.addImage').attr('rel')+'" value="'+result.id+'"/>');
};
fp.removeClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    element.className = element.className.replace(re, ' ').replace(/^\s+|\s+$/g, "");
};
fp.setText = function(element, text){
    element.innerText = text;
    element.textContent = text;
};

fp.children = function(element){
    var children = [],
    child = element.firstChild;

    while (child){
        if (child.nodeType == 1){
            children.push(child);
        }
        child = child.nextSibling;
    }

    return children;
};

fp.getByClass = function(element, className){
    if (element.querySelectorAll){
        return element.querySelectorAll('.' + className);
    }

    var result = [];
    var candidates = element.getElementsByTagName("*");
    var len = candidates.length;

    for (var i = 0; i < len; i++){
        if (fp.hasClass(candidates[i], className)){
            result.push(candidates[i]);
        }
    }
    return result;
};

fp.obj2url = function(obj, temp, prefixDone){
    var uristrings = [],
        prefix = '&',
        add = function(nextObj, i){
            var nextTemp = temp
                ? (/\[\]$/.test(temp)) // prevent double-encoding
                   ? temp
                   : temp+'['+i+']'
                : i;
            if ((nextTemp != 'undefined') && (i != 'undefined')) {
                uristrings.push(
                    (typeof nextObj === 'object')
                        ? fp.obj2url(nextObj, nextTemp, true)
                        : (Object.prototype.toString.call(nextObj) === '[object Function]')
                            ? encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj())
                            : encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj)
                );
            }
        };

    if (!prefixDone && temp) {
      prefix = (/\?/.test(temp)) ? (/\?$/.test(temp)) ? '' : '&' : '?';
      uristrings.push(temp);
      uristrings.push(fp.obj2url(obj));
    } else if ((Object.prototype.toString.call(obj) === '[object Array]') && (typeof obj != 'undefined') ) {
        // we wont use a for-in-loop on an array (performance)
        for (var i = 0, len = obj.length; i < len; ++i){
            add(obj[i], i);
        }
    } else if ((typeof obj != 'undefined') && (obj !== null) && (typeof obj === "object")){
        // for anything else but a scalar, we will use for-in-loop
        for (var i in obj){
            add(obj[i], i);
        }
    } else {
        uristrings.push(encodeURIComponent(temp) + '=' + encodeURIComponent(obj));
    }

    return uristrings.join(prefix)
                     .replace(/^&/, '')
                     .replace(/%20/g, '+');
};


var fp = fp || {};

fp.FileUploaderBasic = function(o){
    this._options = {
        // set to true to see the server response
        debug: false,
        action: '/server/upload',
        params: {},
        customHeaders: {},
        button: null,
        multiple: true,
        maxConnections: 3,
        // validation
        allowedExtensions: ['jpg','jpeg','png','gif'],
        acceptFiles: null,		// comma separated string of mime-types for browser to display in browse dialog
        sizeLimit: 0,
        minSizeLimit: 0,
        // events
        // return false to cancel submit
        onSubmit: function(id, fileName){},
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, responseJSON){},
        onCancel: function(id, fileName){},
        onUpload: function(id, fileName, xhr){},
		onError: function(id, fileName, xhr) {},
        // messages
        messages: {
            typeError: "Unfortunately the file(s) you selected weren't the type we were expecting. Only {extensions} files are allowed.",
            sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
            minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
            emptyError: "{file} is empty, please select files again without it.",
            onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."
        },
        showMessage: function(message){
            alert(message);
        },
        inputName: 'fpfile',
        extraDropzones : []
    };
    fp.extend(this._options, o);
    fp.extend(this, fp.DisposeSupport);

    // number of files being uploaded
    this._filesInProgress = 0;
    this._handler = this._createUploadHandler();

    if (this._options.button){
        this._button = this._createUploadButton(this._options.button);
    }

    this._preventLeaveInProgress();
};

fp.FileUploaderBasic.prototype = {
    setParams: function(params){
        this._options.params = params;
    },
    getInProgress: function(){
        return this._filesInProgress;
    },
    _createUploadButton: function(element){
        var self = this;

        var button = new fp.UploadButton({
            element: element,
            multiple: this._options.multiple && fp.UploadHandlerXhr.isSupported(),
            acceptFiles: this._options.acceptFiles,
            onChange: function(input){
                self._onInputChange(input);
            }
        });

        this.addDisposer(function() { button.dispose(); });
        return button;
    },
    _createUploadHandler: function(){
        var self = this,
            handlerClass;

        if(fp.UploadHandlerXhr.isSupported()){
            handlerClass = 'UploadHandlerXhr';
        } else {
            handlerClass = 'UploadHandlerForm';
        }

        var handler = new fp[handlerClass]({
            debug: this._options.debug,
            action: this._options.action,
            encoding: this._options.encoding,
            maxConnections: this._options.maxConnections,
            customHeaders: this._options.customHeaders,
            inputName: this._options.inputName,
            extraDropzones: this._options.extraDropzones,
            onProgress: function(id, fileName, loaded, total){
                self._onProgress(id, fileName, loaded, total);
                self._options.onProgress(id, fileName, loaded, total);
            },
            onComplete: function(id, fileName, result){
                self._onComplete(id, fileName, result);
                self._options.onComplete(id, fileName, result);
            },
            onCancel: function(id, fileName){
                self._onCancel(id, fileName);
                self._options.onCancel(id, fileName);
            },
            onError: self._options.onError,
            onUpload: function(id, fileName, xhr){
                self._onUpload(id, fileName, xhr);
                self._options.onUpload(id, fileName, xhr);
            }
        });

        return handler;
    },
    _preventLeaveInProgress: function(){
        var self = this;

        this._attach(window, 'beforeunload', function(e){
            if (!self._filesInProgress){return;}

            var e = e || window.event;
            // for ie, ff
            e.returnValue = self._options.messages.onLeave;
            // for webkit
            return self._options.messages.onLeave;
        });
    },
    _onSubmit: function(id, fileName){
        this._filesInProgress++;
    },
    _onProgress: function(id, fileName, loaded, total){
    },
    _onComplete: function(id, fileName, result){
        this._filesInProgress--;
        if (result.error){
            this._options.showMessage(result.error);
        }
    },
    _onCancel: function(id, fileName){
        this._filesInProgress--;
    },
    _onUpload: function(id, fileName, xhr){
    },
    _onInputChange: function(input){
        if (this._handler instanceof fp.UploadHandlerXhr){
            this._uploadFileList(input.files);
        } else {
            if (this._validateFile(input)){
                this._uploadFile(input);
            }
        }
        this._button.reset();
    },
    _uploadFileList: function(files){
        for (var i=0; i<files.length; i++){
            if ( !this._validateFile(files[i])){
                return;
            }
        }

        for (var i=0; i<files.length; i++){
            this._uploadFile(files[i]);
        }
    },
    _uploadFile: function(fileContainer){
        var id = this._handler.add(fileContainer);
        var fileName = this._handler.getName(id);

        if (this._options.onSubmit(id, fileName) !== false){
            this._onSubmit(id, fileName);
            this._handler.upload(id, this._options.params);
        }
    },
    _validateFile: function(file){
        var name, size;

        if (file.value){
            // it is a file input
            // get input value and remove path to normalize
            name = file.value.replace(/.*(\/|\\)/, "");
        } else {
            // fix missing properties in Safari 4 and firefox 11.0a2
            name = (file.fileName !== null && file.fileName !== undefined) ? file.fileName : file.name;
			size = (file.fileSize !== null && file.fileSize !== undefined) ? file.fileSize : file.size;
        }

        if (! this._isAllowedExtension(name)){
            this._error('typeError', name);
            return false;

        } else if (size === 0){
            this._error('emptyError', name);
            return false;

        } else if (size && this._options.sizeLimit && size > this._options.sizeLimit){
            this._error('sizeError', name);
            return false;

        } else if (size && size < this._options.minSizeLimit){
            this._error('minSizeError', name);
            return false;
        }

        return true;
    },
    _error: function(code, fileName){
        var message = this._options.messages[code];
        function r(name, replacement){ message = message.replace(name, replacement); }

        r('{file}', this._formatFileName(fileName));
        r('{extensions}', this._options.allowedExtensions.join(', '));
        r('{sizeLimit}', this._formatSize(this._options.sizeLimit));
        r('{minSizeLimit}', this._formatSize(this._options.minSizeLimit));

        this._options.showMessage(message);
    },
    _formatFileName: function(name){
        if (name.length > 33){
            name = name.slice(0, 19) + '...' + name.slice(-13);
        }
        return name;
    },
    _isAllowedExtension: function(fileName){
        var ext = (-1 !== fileName.indexOf('.'))
					? fileName.replace(/.*[.]/, '').toLowerCase()
					: '';
        var allowed = this._options.allowedExtensions;

        if (!allowed.length){return true;}

        for (var i=0; i<allowed.length; i++){
            if (allowed[i].toLowerCase() == ext){ return true;}
        }

        return false;
    },
    _formatSize: function(bytes){
        var i = -1;
        do {
            bytes = bytes / 1024;
            i++;
        } while (bytes > 99);

        return Math.max(bytes, 0.1).toFixed(1) + ['kB', 'MB', 'GB', 'TB', 'PB', 'EB'][i];
    }
};


/**
 * Class that creates upload widget with drag-and-drop and file list
 * @inherits fp.FileUploaderBasic
 */
fp.FileUploader = function(o){
    // call parent constructor
    fp.FileUploaderBasic.apply(this, arguments);

    // additional options
    fp.extend(this._options, {
        element: null,
        // if set, will be used instead of fp-upload-list in template
        listElement: null,
        dragText: 'Перетащите файл сюда',
        uploadButtonText: 'Добавить картинку',
        cancelButtonText: 'Отмена',
        failUploadText: 'Ошибка загрузки',

        template: '<div class="fp-uploader">' +
                '<div class="fp-upload-drop-area">{dragText}</div>' +
                '<div class="fp-upload-button bt violet">{uploadButtonText}</div>' +
                '<ul class="fp-upload-list"></ul>' +
             '</div>',

        // template for one item in file list
        fileTemplate: '<li>' +
                '<div class="progress_bar"><div class="fp-progress-bar line"></div></div>' +
                '<span class="fp-upload-file"></span>' +
                '<span class="fp-upload-spinner"></span>' +
                '<span class="fp-upload-size"></span>' +
                '<a class="fp-upload-cancel" href="#">{cancelButtonText}</a>' +
                '<span class="fp-upload-failed-text">{failUploadtext}</span>' +
            '</li>',

        classes: {
            // used to get elements from templates
            button: 'fp-upload-button',
            drop: 'fp-upload-drop-area',
            dropActive: 'fp-upload-drop-area-active',
            dropDisabled: 'fp-upload-drop-area-disabled',
            list: 'fp-upload-list',
            progressBar: 'fp-progress-bar',
            file: 'fp-upload-file',
            spinner: 'fp-upload-spinner',
            size: 'fp-upload-size',
            cancel: 'fp-upload-cancel',

            // added to list item <li> when upload completes
            // used in css to hide progress spinner
            success: 'fp-upload-success',
            fail: 'fp-upload-fail'
        }
    });
    // overwrite options with user supplied
    fp.extend(this._options, o);

    // overwrite the upload button text if any
    // same for the Cancel button and Fail message text
    this._options.template     = this._options.template.replace(/\{dragText\}/g, this._options.dragText);
    this._options.template     = this._options.template.replace(/\{uploadButtonText\}/g, this._options.uploadButtonText);
    this._options.fileTemplate = this._options.fileTemplate.replace(/\{cancelButtonText\}/g, this._options.cancelButtonText);
    this._options.fileTemplate = this._options.fileTemplate.replace(/\{failUploadtext\}/g, this._options.failUploadText);

    this._element = this._options.element;
    this._element.innerHTML = this._options.template;
    this._listElement = this._options.listElement || this._find(this._element, 'list');

    this._classes = this._options.classes;

    this._button = this._createUploadButton(this._find(this._element, 'button'));

    this._bindCancelEvent();
    this._setupDragDrop();
};

// inherit from Basic Uploader
fp.extend(fp.FileUploader.prototype, fp.FileUploaderBasic.prototype);

fp.extend(fp.FileUploader.prototype, {
    addExtraDropzone: function(element){
        this._setupExtraDropzone(element);
    },
    removeExtraDropzone: function(element){
        var dzs = this._options.extraDropzones;
        for(var i in dzs) if (dzs[i] === element) return this._options.extraDropzones.splice(i,1);
    },
    _leaving_document_out: function(e){
        return ((fp.chrome() || (fp.safari() && fp.windows())) && e.clientX == 0 && e.clientY == 0) // null coords for Chrome and Safari Windows
             || (fp.firefox() && !e.relatedTarget); // null e.relatedTarget for Firefox
     },
    /**
     * Gets one of the elements listed in this._options.classes
     **/
    _find: function(parent, type){
        var element = fp.getByClass(parent, this._options.classes[type])[0];
        if (!element){
            throw new Error('element not found ' + type);
        }

        return element;
    },
    _setupExtraDropzone: function(element){
        this._options.extraDropzones.push(element);
        this._setupDropzone(element);
    },
    _setupDropzone: function(dropArea){
        var self = this;

        var dz = new fp.UploadDropZone({
            element: dropArea,
            onEnter: function(e){
                fp.addClass(dropArea, self._classes.dropActive);
                e.stopPropagation();
            },
            onLeave: function(e){
                //e.stopPropagation();
            },
            onLeaveNotDescendants: function(e){
                fp.removeClass(dropArea, self._classes.dropActive);
            },
            onDrop: function(e){
                dropArea.style.display = 'none';
                fp.removeClass(dropArea, self._classes.dropActive);
                self._uploadFileList(e.dataTransfer.files);
            }
        });

		this.addDisposer(function() { dz.dispose(); });

		dropArea.style.display = 'none';
    },
    _setupDragDrop: function(){
        var dropArea = this._find(this._element, 'drop');
		var self = this;
        this._options.extraDropzones.push(dropArea);

        var dropzones = this._options.extraDropzones;
        var i;
        for (i=0; i < dropzones.length; i++){
            this._setupDropzone(dropzones[i]);
        }

		// IE <= 9 does not support the File API used for drag+drop uploads
		// Any volunteers to enable & test this for IE10?
		if (!fp.ie()) {
			this._attach(document, 'dragenter', function(e){
				if (fp.hasClass(dropArea, self._classes.dropDisabled)) return;

				dropArea.style.display = 'block';
				for (i=0; i < dropzones.length; i++){ dropzones[i].style.display = 'block'; }

			});
		}
        this._attach(document, 'dragleave', function(e){
            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // only fire when leaving document out
            if (fp.FileUploader.prototype._leaving_document_out(e)) {
                for (i=0; i < dropzones.length; i++){ dropzones[i].style.display = 'none'; }
            }
        });
        fp.attach(document, 'drop', function(e){
          for (i=0; i < dropzones.length; i++){ dropzones[i].style.display = 'none'; }
          e.preventDefault();
        });
    },
    _onSubmit: function(id, fileName){
		$('.fp-upload-list').show();
        fp.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
        this._addToList(id, fileName);
    },
	// Update the progress bar & percentage as the file is uploaded
    _onProgress: function(id, fileName, loaded, total){
        fp.FileUploaderBasic.prototype._onProgress.apply(this, arguments);

        var item = this._getItemByFileId(id);
        var size = this._find(item, 'size');
        size.style.display = 'inline';

        var text;
		var percent = Math.round(loaded / total * 100);

        if (loaded != total) {
			// If still uploading, display percentage
            text = percent + '% from ' + this._formatSize(total);
        } else {
			// If complete, just display final size
            text = this._formatSize(total);
        }

		// Update progress bar <span> tag
		this._find(item, 'progressBar').style.width = percent + '%';

        fp.setText(size, text);
    },
    _onComplete: function(id, fileName, result){
        fp.FileUploaderBasic.prototype._onComplete.apply(this, arguments);

        // mark completed
        var item = this._getItemByFileId(id);
        fp.remove(this._find(item, 'cancel'));
        fp.remove(this._find(item, 'spinner'));
        if (result.success){
            fp.addClass(item, this._classes.success);
			fp.addImage(item,result);
        } else {
            fp.addClass(item, this._classes.fail);
        }
    },
    _addToList: function(id, fileName){
        var item = fp.toElement(this._options.fileTemplate);
        item.fpFileId = id;

        var fileElement = this._find(item, 'file');
        fp.setText(fileElement, this._formatFileName(fileName));
        this._find(item, 'size').style.display = 'none';
		if (!this._options.multiple) this._clearList();
        this._listElement.appendChild(item);
    },
	_clearList: function(){
		this._listElement.innerHTML = '';
	},
    _getItemByFileId: function(id){
        var item = this._listElement.firstChild;

        // there can't be txt nodes in dynamically created list
        // and we can  use nextSibling
        while (item){
            if (item.fpFileId == id) return item;
            item = item.nextSibling;
        }
    },
    /**
     * delegate click event for cancel link
     **/
    _bindCancelEvent: function(){
        var self = this,
            list = this._listElement;

        this._attach(list, 'click', function(e){
            e = e || window.event;
            var target = e.target || e.srcElement;

            if (fp.hasClass(target, self._classes.cancel)){
                fp.preventDefault(e);

                var item = target.parentNode;
                self._handler.cancel(item.fpFileId);
                fp.remove(item);
            }
        });
    }
});

fp.UploadDropZone = function(o){
    this._options = {
        element: null,
        onEnter: function(e){},
        onLeave: function(e){},
        // is not fired when leaving element by hovering descendants
        onLeaveNotDescendants: function(e){},
        onDrop: function(e){}
    };
    fp.extend(this._options, o);
    fp.extend(this, fp.DisposeSupport);

    this._element = this._options.element;

    this._disableDropOutside();
    this._attachEvents();
};

fp.UploadDropZone.prototype = {
    _dragover_should_be_canceled: function(){
        return fp.safari() || (fp.firefox() && fp.windows());
    },
    _disableDropOutside: function(e){
        // run only once for all instances
        if (!fp.UploadDropZone.dropOutsideDisabled ){

            // for these cases we need to catch onDrop to reset dropArea
            if (this._dragover_should_be_canceled){
            fp.attach(document, 'dragover', function(e){
                    e.preventDefault();
                });
            } else {
                fp.attach(document, 'dragover', function(e){
                if (e.dataTransfer){
                    e.dataTransfer.dropEffect = 'none';
                    e.preventDefault();
                    }
            });
            }

            fp.UploadDropZone.dropOutsideDisabled = true;
        }
    },
    _attachEvents: function(){
        var self = this;

        self._attach(self._element, 'dragover', function(e){
            if (!self._isValidFileDrag(e)) return;

            var effect = fp.ie() ? null : e.dataTransfer.effectAllowed;
            if (effect == 'move' || effect == 'linkMove'){
                e.dataTransfer.dropEffect = 'move'; // for FF (only move allowed)
            } else {
                e.dataTransfer.dropEffect = 'copy'; // for Chrome
            }

            e.stopPropagation();
            e.preventDefault();
        });

        self._attach(self._element, 'dragenter', function(e){
            if (!self._isValidFileDrag(e)) return;

            self._options.onEnter(e);
        });

        self._attach(self._element, 'dragleave', function(e){
            if (!self._isValidFileDrag(e)) return;

            self._options.onLeave(e);

            var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
            // do not fire when moving a mouse over a descendant
            if (fp.contains(this, relatedTarget)) return;

            self._options.onLeaveNotDescendants(e);
        });

        self._attach(self._element, 'drop', function(e){
            if (!self._isValidFileDrag(e)) return;

            e.preventDefault();
            self._options.onDrop(e);
        });
    },
    _isValidFileDrag: function(e){
		// e.dataTransfer currently causing IE errors
		// IE9 does NOT support file API, so drag-and-drop is not possible
		// IE10 should work, but currently has not been tested - any volunteers?
		if (fp.ie()) return false;

        var dt = e.dataTransfer,
            // do not check dt.types.contains in webkit, because it crashes safari 4
            isSafari = fp.safari();

        // dt.effectAllowed is none in Safari 5
        // dt.types.contains check is for firefox
        return dt && dt.effectAllowed != 'none' &&
            (dt.files || (!isSafari && dt.types.contains && dt.types.contains('Files')));

    }
};

fp.UploadButton = function(o){
    this._options = {
        element: null,
        // if set to true adds multiple attribute to file input
        multiple: false,
        acceptFiles: null,
        // name attribute of file input
        name: 'file',
        onChange: function(input){},
        hoverClass: 'fp-upload-button-hover',
        focusClass: 'fp-upload-button-focus'
    };

    fp.extend(this._options, o);
    fp.extend(this, fp.DisposeSupport);

    this._element = this._options.element;

    // make button suitable container for input
    fp.css(this._element, {
        position: 'relative',
        overflow: 'hidden',
        // Make sure browse button is in the right side
        // in Internet Explorer
        direction: 'ltr'
    });

    this._input = this._createInput();
};

fp.UploadButton.prototype = {
    /* returns file input element */
    getInput: function(){
        return this._input;
    },
    /* cleans/recreates the file input */
    reset: function(){
        if (this._input.parentNode){
            fp.remove(this._input);
        }

        fp.removeClass(this._element, this._options.focusClass);
        this._input = this._createInput();
    },
    _createInput: function(){
        var input = document.createElement("input");

        if (this._options.multiple){
            input.setAttribute("multiple", "multiple");
        }

        if (this._options.acceptFiles) input.setAttribute("accept", this._options.acceptFiles);

        input.setAttribute("type", "file");
        input.setAttribute("name", this._options.name);

        fp.css(input, {
            position: 'absolute',
            // in Opera only 'browse' button
            // is clickable and it is located at
            // the right side of the input
            right: 0,
            top: 0,
            fontFamily: 'Arial',
            // 4 persons reported this, the max values that worked for them were 243, 236, 236, 118
            fontSize: '118px',
            margin: 0,
            padding: 0,
            cursor: 'pointer',
            opacity: 0
        });

        this._element.appendChild(input);

        var self = this;
        this._attach(input, 'change', function(){
            self._options.onChange(input);
        });

        this._attach(input, 'mouseover', function(){
            fp.addClass(self._element, self._options.hoverClass);
        });
        this._attach(input, 'mouseout', function(){
            fp.removeClass(self._element, self._options.hoverClass);
        });
        this._attach(input, 'focus', function(){
            fp.addClass(self._element, self._options.focusClass);
        });
        this._attach(input, 'blur', function(){
            fp.removeClass(self._element, self._options.focusClass);
        });

        // IE and Opera, unfortunately have 2 tab stops on file input
        // which is unacceptable in our case, disable keyboard access
        if (window.attachEvent){
            // it is IE or Opera
            input.setAttribute('tabIndex', "-1");
        }

        return input;
    }
};

/**
 * Class for uploading files, uploading itself is handled by child classes
 */
fp.UploadHandlerAbstract = function(o){
	// Default options, can be overridden by the user
    this._options = {
        debug: false,
        action: '/upload.php',
        // maximum number of concurrent uploads
        maxConnections: 999,
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, response){},
        onCancel: function(id, fileName){},
        onUpload: function(id, fileName, xhr){}
    };
    fp.extend(this._options, o);

    this._queue = [];
    // params for files in queue
    this._params = [];
};
fp.UploadHandlerAbstract.prototype = {
    log: function(str){
        if (this._options.debug && window.console) console.log('[uploader] ' + str);
    },
    /**
     * Adds file or file input to the queue
     * @returns id
     **/
    add: function(file){},
    /**
     * Sends the file identified by id and additional query params to the server
     */
    upload: function(id, params){
        var len = this._queue.push(id);

        var copy = {};
        fp.extend(copy, params);
        this._params[id] = copy;

        // if too many active uploads, wait...
        if (len <= this._options.maxConnections){
            this._upload(id, this._params[id]);
        }
    },
    /**
     * Cancels file upload by id
     */
    cancel: function(id){
        this._cancel(id);
        this._dequeue(id);
    },
    /**
     * Cancells all uploads
     */
    cancelAll: function(){
        for (var i=0; i<this._queue.length; i++){
            this._cancel(this._queue[i]);
        }
        this._queue = [];
    },
    /**
     * Returns name of the file identified by id
     */
    getName: function(id){},
    /**
     * Returns size of the file identified by id
     */
    getSize: function(id){},
    /**
     * Returns id of files being uploaded or
     * waiting for their turn
     */
    getQueue: function(){
        return this._queue;
    },
    /**
     * Actual upload method
     */
    _upload: function(id){},
    /**
     * Actual cancel method
     */
    _cancel: function(id){},
    /**
     * Removes element from queue, starts upload of next
     */
    _dequeue: function(id){
        var i = fp.indexOf(this._queue, id);
        this._queue.splice(i, 1);

        var max = this._options.maxConnections;

        if (this._queue.length >= max && i < max){
            var nextId = this._queue[max-1];
            this._upload(nextId, this._params[nextId]);
        }
    }
};

/**
 * Class for uploading files using form and iframe
 * @inherits fp.UploadHandlerAbstract
 */
fp.UploadHandlerForm = function(o){
    fp.UploadHandlerAbstract.apply(this, arguments);

    this._inputs = {};
};
// @inherits fp.UploadHandlerAbstract
fp.extend(fp.UploadHandlerForm.prototype, fp.UploadHandlerAbstract.prototype);

fp.extend(fp.UploadHandlerForm.prototype, {
    add: function(fileInput){
        fileInput.setAttribute('name', this._options.inputName);
        var id = 'fp-upload-handler-iframe' + fp.getUniqueId();

        this._inputs[id] = fileInput;

        // remove file input from DOM
        if (fileInput.parentNode){
            fp.remove(fileInput);
        }

        return id;
    },
    getName: function(id){
        // get input value and remove path to normalize
        return this._inputs[id].value.replace(/.*(\/|\\)/, "");
    },
    _cancel: function(id){
        this._options.onCancel(id, this.getName(id));

        delete this._inputs[id];

        var iframe = document.getElementById(id);
        if (iframe){
            // to cancel request set src to something else
            // we use src="javascript:false;" because it doesn't
            // trigger ie6 prompt on https
            iframe.setAttribute('src', 'javascript:false;');

            fp.remove(iframe);
        }
    },
    _upload: function(id, params){
        this._options.onUpload(id, this.getName(id), false);
        var input = this._inputs[id];

        if (!input){
            throw new Error('file with passed id was not added, or already uploaded or cancelled');
        }

        var fileName = this.getName(id);

        var iframe = this._createIframe(id);
        var form = this._createForm(iframe, params);
        form.appendChild(input);

        var self = this;
        this._attachLoadEvent(iframe, function(){
            self.log('iframe loaded');

            var response = self._getIframeContentJSON(iframe);

            self._options.onComplete(id, fileName, response);
            self._dequeue(id);

            delete self._inputs[id];
            // timeout added to fix busy state in FF3.6
            setTimeout(function(){
                self._detach_event();
                fp.remove(iframe);
            }, 1);
        });

        form.submit();
        fp.remove(form);

        return id;
    },
    _attachLoadEvent: function(iframe, callback){
        this._detach_event = fp.attach(iframe, 'load', function(){
            // when we remove iframe from dom
            // the request stops, but in IE load
            // event fires
            if (!iframe.parentNode){
                return;
            }

            // fixing Opera 10.53
            if (iframe.contentDocument &&
                iframe.contentDocument.body &&
                iframe.contentDocument.body.innerHTML == "false"){
                // In Opera event is fired second time
                // when body.innerHTML changed from false
                // to server response approx. after 1 sec
                // when we upload file with iframe
                return;
            }

            callback();
        });
    },
    /**
     * Returns json object received by iframe from server.
     */
    _getIframeContentJSON: function(iframe){
        // iframe.contentWindow.document - for IE<7
        var doc = iframe.contentDocument ? iframe.contentDocument: iframe.contentWindow.document,
            response;

        var innerHTML = doc.body.innerHTML;
        this.log("converting iframe's innerHTML to JSON");
        this.log("innerHTML = " + innerHTML);
        //plain text response may be wrapped in <pre> tag
        if (innerHTML.slice(0, 5).toLowerCase() == '<pre>' && innerHTML.slice(-6).toLowerCase() == '</pre>') {
          innerHTML = doc.body.firstChild.firstChild.nodeValue;
        }

        try {
            response = eval("(" + innerHTML + ")");
        } catch(err){
            response = {};
        }

        return response;
    },
    /**
     * Creates iframe with unique name
     */
    _createIframe: function(id){
        // We can't use following code as the name attribute
        // won't be properly registered in IE6, and new window
        // on form submit will open
        // var iframe = document.createElement('iframe');
        // iframe.setAttribute('name', id);

        var iframe = fp.toElement('<iframe src="javascript:false;" name="' + id + '" />');
        // src="javascript:false;" removes ie6 prompt on https

        iframe.setAttribute('id', id);

        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        return iframe;
    },
    /**
     * Creates form, that will be submitted to iframe
     */
    _createForm: function(iframe, params){
        // We can't use the following code in IE6
        // var form = document.createElement('form');
        // form.setAttribute('method', 'post');
        // form.setAttribute('enctype', 'multipart/form-data');
        // Because in this case file won't be attached to request
        var form = fp.toElement('<form method="post" enctype="multipart/form-data"></form>');

        var queryString = fp.obj2url(params, this._options.action);

        form.setAttribute('action', queryString);
        form.setAttribute('target', iframe.name);
        form.style.display = 'none';
        document.body.appendChild(form);

        return form;
    }
});

/**
 * Class for uploading files using xhr
 * @inherits fp.UploadHandlerAbstract
 */
fp.UploadHandlerXhr = function(o){
    fp.UploadHandlerAbstract.apply(this, arguments);

    this._files = [];
    this._xhrs = [];

    // current loaded size in bytes for each file
    this._loaded = [];
};

// static method
fp.UploadHandlerXhr.isSupported = function(){
    var input = document.createElement('input');
    input.type = 'file';

    return (
        'multiple' in input &&
        typeof File != "undefined" &&
        typeof FormData != "undefined" &&
        typeof (new XMLHttpRequest()).upload != "undefined" );
};

// @inherits fp.UploadHandlerAbstract
fp.extend(fp.UploadHandlerXhr.prototype, fp.UploadHandlerAbstract.prototype)

fp.extend(fp.UploadHandlerXhr.prototype, {
    /**
     * Adds file to the queue
     * Returns id to use with upload, cancel
     **/
    add: function(file){
        if (!(file instanceof File)){
            throw new Error('Passed obj in not a File (in fp.UploadHandlerXhr)');
        }

        return this._files.push(file) - 1;
    },
    getName: function(id){
        var file = this._files[id];
        // fix missing name in Safari 4
        //NOTE: fixed missing name firefox 11.0a2 file.fileName is actually undefined
        return (file.fileName !== null && file.fileName !== undefined) ? file.fileName : file.name;
    },
    getSize: function(id){
        var file = this._files[id];
        return file.fileSize != null ? file.fileSize : file.size;
    },
    /**
     * Returns uploaded bytes for file identified by id
     */
    getLoaded: function(id){
        return this._loaded[id] || 0;
    },
    /**
     * Sends the file identified by id and additional query params to the server
     * @param {Object} params name-value string pairs
     */
    _upload: function(id, params){
        this._options.onUpload(id, this.getName(id), true);

        var file = this._files[id],
            name = this.getName(id),
            size = this.getSize(id);

        this._loaded[id] = 0;

        var xhr = this._xhrs[id] = new XMLHttpRequest();
        var self = this;

        xhr.upload.onprogress = function(e){
            if (e.lengthComputable){
                self._loaded[id] = e.loaded;
                self._options.onProgress(id, name, e.loaded, e.total);
            }
        };

        xhr.onreadystatechange = function(){
            if (xhr.readyState == 4){
                self._onComplete(id, xhr);
            }
        };

        // build query string
        params = params || {};
        params[this._options.inputName] = name;
        var queryString = fp.obj2url(params, this._options.action);

        xhr.open("POST", queryString, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-File-Name", encodeURIComponent(name));
        if (this._options.encoding == 'multipart') {
            var formData = new FormData();
            formData.append(name, file);
            file = formData;
        } else {
            xhr.setRequestHeader("Content-Type", "application/octet-stream");
            //NOTE: return mime type in xhr works on chrome 16.0.9 firefox 11.0a2
            xhr.setRequestHeader("X-Mime-Type",file.type );
        }
        for (key in this._options.customHeaders){
            xhr.setRequestHeader(key, this._options.customHeaders[key]);
        };
        xhr.send(file);
    },
    _onComplete: function(id, xhr){
        // the request was aborted/cancelled
        if (!this._files[id]) return;

        var name = this.getName(id);
        var size = this.getSize(id);

        this._options.onProgress(id, name, size, size);

        if (xhr.status == 200){
            this.log("xhr - server response received");
            this.log("responseText = " + xhr.responseText);

            var response;

            try {
                response = eval("(" + xhr.responseText + ")");
            } catch(err){
                response = {};
            }

            this._options.onComplete(id, name, response);

        } else {
            this._options.onError(id, name, xhr);
            this._options.onComplete(id, name, {});
        }

        this._files[id] = null;
        this._xhrs[id] = null;
        this._dequeue(id);
    },
    _cancel: function(id){
        this._options.onCancel(id, this.getName(id));

        this._files[id] = null;

        if (this._xhrs[id]){
            this._xhrs[id].abort();
            this._xhrs[id] = null;
        }
    }
});

/**
 * A generic module which supports object disposing in dispose() method.
 * */
fp.DisposeSupport = {
  _disposers: [],

  /** Run all registered disposers */
  dispose: function() {
    var disposer;
    while (disposer = this._disposers.shift()) {
      disposer();
    }
  },

  /** Add disposer to the collection */
  addDisposer: function(disposeFunction) {
    this._disposers.push(disposeFunction);
  },

  /** Attach event handler and register de-attacher as a disposer */
  _attach: function() {
    this.addDisposer(fp.attach.apply(this, arguments));
  }
};
