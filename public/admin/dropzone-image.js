(function () {
    var ROOT_SELECTOR = '[data-dropzone-image]';
    var INPUT_SELECTOR = '[data-dropzone-image-input]';
    var PREVIEW_IMAGE_SELECTOR = '[data-dropzone-image-preview-image]';
    var PLACEHOLDER_SELECTOR = '[data-dropzone-image-placeholder]';
    var FILENAME_SELECTOR = '[data-dropzone-image-filename]';
    var ACTIVE_CLASS = 'is-dragover';
    var INITIALIZED_KEY = '__dropzoneImageInitialized';
    var OBJECT_URL_KEY = '__dropzoneImageObjectUrl';
    var CURRENT_SRC_KEY = '__dropzoneImageCurrentSrc';

    function formatFileSize(bytes) {
        if (!bytes && bytes !== 0) {
            return '';
        }

        if (bytes < 1024) {
            return bytes + ' B';
        }

        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function revokeObjectUrl(root) {
        if (root[OBJECT_URL_KEY]) {
            URL.revokeObjectURL(root[OBJECT_URL_KEY]);
            root[OBJECT_URL_KEY] = null;
        }
    }

    function getFirstFile(fileList) {
        if (!fileList || !fileList.length) {
            return null;
        }

        return fileList[0];
    }

    function setPreviewVisibility(root, hasPreview) {
        var previewImage = root.querySelector(PREVIEW_IMAGE_SELECTOR);
        var placeholder = root.querySelector(PLACEHOLDER_SELECTOR);

        if (previewImage) {
            previewImage.hidden = !hasPreview;
        }

        if (placeholder) {
            placeholder.hidden = hasPreview;
        }
    }

    function setFilename(root, text) {
        var filename = root.querySelector(FILENAME_SELECTOR);
        if (filename) {
            filename.textContent = text;
        }
    }

    function setPreviewFromExisting(root, input) {
        var previewImage = root.querySelector(PREVIEW_IMAGE_SELECTOR);
        var currentSrc = input.dataset.dropzoneImageCurrentSrc || '';

        revokeObjectUrl(root);
        root[CURRENT_SRC_KEY] = currentSrc;

        if (previewImage && currentSrc) {
            previewImage.src = currentSrc;
            setPreviewVisibility(root, true);
        } else {
            setPreviewVisibility(root, false);
        }
    }

    function setPreviewFromFile(root, input, file) {
        var previewImage = root.querySelector(PREVIEW_IMAGE_SELECTOR);

        revokeObjectUrl(root);

        if (previewImage && file && file.type.indexOf('image/') === 0) {
            var objectUrl = URL.createObjectURL(file);
            root[OBJECT_URL_KEY] = objectUrl;
            previewImage.src = objectUrl;
            setPreviewVisibility(root, true);
        } else if (root[CURRENT_SRC_KEY]) {
            if (previewImage) {
                previewImage.src = root[CURRENT_SRC_KEY];
            }
            setPreviewVisibility(root, true);
        } else {
            setPreviewVisibility(root, false);
        }

        if (file) {
            setFilename(root, file.name + ' (' + formatFileSize(file.size) + ')');
        } else if (root[CURRENT_SRC_KEY]) {
            setFilename(root, 'Current image');
        } else {
            setFilename(root, 'No file selected');
        }
    }

    function updateInputFiles(input, file) {
        var dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function activateDragState(root) {
        root.classList.add(ACTIVE_CLASS);
    }

    function deactivateDragState(root) {
        root.classList.remove(ACTIVE_CLASS);
    }

    function initRoot(root) {
        if (!root || root[INITIALIZED_KEY]) {
            return;
        }

        var input = root.querySelector(INPUT_SELECTOR);
        if (!input) {
            return;
        }

        root[INITIALIZED_KEY] = true;

        setPreviewFromExisting(root, input);

        root.setAttribute('tabindex', root.getAttribute('tabindex') || '0');
        root.setAttribute('role', 'button');
        root.setAttribute('aria-label', 'Image upload drop zone');

        root.addEventListener('click', function (event) {
            if (event.target instanceof Element && event.target.closest('input,button,a,label,select,textarea')) {
                return;
            }

            input.click();
        });

        root.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                input.click();
            }
        });

        input.addEventListener('change', function () {
            var file = getFirstFile(input.files);
            setPreviewFromFile(root, input, file);
        });

        root.addEventListener('dragenter', function (event) {
            event.preventDefault();
            event.stopPropagation();
            activateDragState(root);
            root.__dropzoneDragDepth = (root.__dropzoneDragDepth || 0) + 1;
        });

        root.addEventListener('dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            activateDragState(root);
            if (event.dataTransfer) {
                event.dataTransfer.dropEffect = 'copy';
            }
        });

        root.addEventListener('dragleave', function (event) {
            event.preventDefault();
            event.stopPropagation();
            root.__dropzoneDragDepth = Math.max((root.__dropzoneDragDepth || 1) - 1, 0);
            if (root.__dropzoneDragDepth === 0) {
                deactivateDragState(root);
            }
        });

        root.addEventListener('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            root.__dropzoneDragDepth = 0;
            deactivateDragState(root);

            var file = getFirstFile(event.dataTransfer && event.dataTransfer.files);
            if (!file) {
                return;
            }

            updateInputFiles(input, file);
        });
    }

    function initAll(container) {
        (container || document).querySelectorAll(ROOT_SELECTOR).forEach(initRoot);
    }

    function watchDom() {
        if (window.__dropzoneImageObserver) {
            return;
        }

        window.__dropzoneImageObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== Node.ELEMENT_NODE) {
                        return;
                    }

                    if (node.matches && node.matches(ROOT_SELECTOR)) {
                        initRoot(node);
                    }

                    if (node.querySelectorAll) {
                        initAll(node);
                    }
                });
            });
        });

        window.__dropzoneImageObserver.observe(document.documentElement, {
            childList: true,
            subtree: true,
        });
    }

    function cleanupRoots() {
        document.querySelectorAll(ROOT_SELECTOR).forEach(function (root) {
            revokeObjectUrl(root);
            deactivateDragState(root);
            root.__dropzoneDragDepth = 0;
        });
    }

    document.addEventListener('dragover', function (event) {
        event.preventDefault();
    }, true);

    document.addEventListener('drop', function (event) {
        event.preventDefault();
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);
        watchDom();
    });

    document.addEventListener('turbo:load', function () {
        initAll(document);
    });

    document.addEventListener('ea.collection.item-added', function (event) {
        if (event.detail && event.detail.newElement) {
            initAll(event.detail.newElement);
            return;
        }

        initAll(document);
    });

    document.addEventListener('turbo:before-cache', function () {
        cleanupRoots();
    });

    if (document.readyState !== 'loading') {
        initAll(document);
        watchDom();
    }
})();
