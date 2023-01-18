"use strict";

(function ($)
{
  let fmUrl = '',
    fileSortKey = 'newest',
    fileType = 'image',
    activeFileClass = 'selected border border-primary',
    fileSearchKey = null,
    dataLoaded = false,
    fmData = {},
    uploadBtnAction = null,
    uploaderModal = null;

  fmData = clearFileSelectors();

  // ----------------------------------------------------------------
  // ---------------- jQuery DOM Event Handle Block -----------------
  // ----------------------------------------------------------------

  // File(s) upload-select event with modal pop-up action
  $(document).on('click', '.nxt-upload-button', function ()
  {
    let modal = $(this).data('modal');

    if ($(modal).length > 0) {
      uploaderModal = $(modal);

      uploaderModal.modal('show');
      uploaderModal.find('.file-preview-control').addClass('d-none');

      uploadBtnAction = prepareActionElement($(this));

      let preSelections = uploadBtnAction.find('.selected-files').val();

      if (preSelections.trim() !== '') {
        preSelections = (preSelections.trim()).split(',').map((num) => parseInt(num))

        if (preSelections !== fmData.selectedFiles) {
          fmData.selectedFiles = preSelections;
        }
      }

      fmData.multiple = parseBoolean($(this).data('multiple'));
      fileType = $(this).data('type');

      fmUrl = uploaderModal.find('.nxt-filter-form').data('url');

      if (!dataLoaded) {
        getAllData(fmUrl)
        dataLoaded = true;
      }
    }

  });

  // File manager section tab navigation event inside modal
  $(document).on('click', '.uploader-nav-menu', function ()
  {
    $('.uploader-nav-menu-link').removeClass('active');

    $(uploaderModal).find('.tab-pane').removeClass('active');

    $(this).children('a.uploader-nav-menu-link').addClass('active');
    let tabId = $(this).find('a.nav-link').data('tab');
    $(tabId).addClass('active');
  });

  // File manager file select event inside modal
  $(document).on('click', '.upload-content .attachment-card', function ()
  {
    let selected = $(this).hasClass('selected')

    let value = $(this).data('value');

    let valueObject =
      fmData.allFiles[
      fmData.allFiles.findIndex(
        (x) => x.id === value
      )
      ];

    if (!selected && !fmData.multiple) {
      uploaderModal.find('.file-preview-control').removeClass('d-none');
      let t = uploaderModal.find('.file-preview-control')
        .find('a').attr('href', $(this).data('fileurl')).attr('target', '_BLANK');
    } else {
      uploaderModal.find('.file-preview-control').addClass('d-none');
    }

    if (fmData.multiple) {
      if (selected) {
        $(this).removeClass(activeFileClass);
        let index = fmData.selectedFiles.indexOf(value);
        if (index !== -1) {
          fmData.selectedFiles.splice(index, 1);
          fmData.selectedFilesObject.splice(index, 1);
        }
      } else {
        $(this).addClass(activeFileClass);
        fmData.selectedFiles.push(value);
        fmData.selectedFilesObject.push(valueObject);
      }
    } else {
      if (selected) {
        $(this).removeClass(activeFileClass);
        fmData.selectedFiles = []
        fmData.selectedFilesObject = []
      } else {
        $('.attachment-card.selected').removeClass(activeFileClass);

        fmData.selectedFiles = []
        fmData.selectedFilesObject = []

        $(this).addClass(activeFileClass);
        fmData.selectedFiles.push(value);
        fmData.selectedFilesObject.push(valueObject);
      }
    }

    uploaderModal.find('.count-result').html(
      fmData.selectedFiles.length
    );

  });

  // Clear all selected files inside file manager modal
  $(document).on('click', '.clear-selected', uploaderModal, function ()
  {

    fmData = clearFileSelectors()

    uploaderModal.find('.nxt-filter-form').trigger("reset");

    getAllData(fmUrl);

    uploaderModal.find('.count-result').html(0);

  });

  // preview file
  // $(document).on('click', '.file-preview-control > a', uploaderModal, function ()
  // {



  // });

  // Pagination next page control action for file manager
  $(document).on('click', '.uploader-next-btn', uploaderModal, function ()
  {
    getAllData(fmData.nextUrl)
  });

  // Pagination previous page control action for file manager
  $(document).on('click', '.uploader-prev-btn', uploaderModal, function ()
  {
    getAllData(fmData.previousUrl)
  });

  // File(s) add event: Insert file data to form input and preview
  $(document).on('click', '.uploader-footer .file-add', function ()
  {

    uploadBtnAction.find('input.selected-files').val(fmData.selectedFiles);
    uploadBtnAction.find('.file-amount').html(fmData.selectedFiles.length + ' file selected');

    uploaderModal.modal('hide');

    if (fmData.selectedFiles.length > 0) {
      let previewBlock = uploadBtnAction.parent('div').find('.previews');
      let imgUrl = uploadBtnAction.data('fileurl');

      previewBlock.html('');

      if (previewBlock && imgUrl) {

        for (const i in fmData.selectedFiles) {
          let file = fmData.selectedFiles[i];
          let imageItem = `<div class="image-item" data-id="${file}">`
            + `<img src="${imgUrl + '/' + file}" />`
            // + `<div class="title">${fileType}</div>`
            + `<span class="remove">x</span>`
            + `</div>`;

          previewBlock.append(imageItem);

        }
      }
    }

    $('#uploadedFileGallery').html('');
    updateProgress(0, 0);

  });

  // File search acton filter with pagination
  $(document).on('keyup', '.uploader-search input.file-search', function ()
  {
    fileSearchKey = $(this).val();
    getAllData(fmUrl)
  });

  // File sort action filter with pagination
  $(document).on('change', '.uploader-filter select.file-sort', function ()
  {
    fileSortKey = $(this).val();
    getAllData(fmUrl)
  });

  // Event to show only selected files in file manager modal
  $(document).on('change', '.uploader-filter input.selected-file', function ()
  {
    if ($(this).is(":checked")) {
      getAllData(fmUrl, fmData.selectedFiles)
    } else {
      getAllData(fmUrl)
    }
  })

  // Remove selected file from input upon item remove click event
  $(document).on('click', '.image-item .remove', function ()
  {
    let image = $(this).parent('.image-item');

    uploadBtnAction = $(this).closest('.file-input-wrapper').find('.nxt-upload-button');

    let selected = uploadBtnAction.find('input.selected-files').val().split(',').map(Number);

    removeInputValue(
      image.data("id"),
      selected
    );

    image.remove();
  })

  /* ------------ END - jQuery DOM Event Handle Block ------------- */

  // ----------------------------------------------------------------
  // ------------------- Custom Functions Block --------------------
  // ----------------------------------------------------------------

  /**
   * Remove selected file data
   *
   * @param {number} id File ID
   * @param {object} array File array
   */
  function removeInputValue(id, array)
  {
    const selected = array.filter(function (item)
    {
      return item !== parseInt(id);
    });

    fmData.selectedFiles = selected

    addSelectedValue()

    if (selected.length > 0) {
      uploadBtnAction.find(".file-amount").html(selected.length + ' file selected');
    } else {
      uploadBtnAction.find(".file-amount").html('Choose File');
    }
    uploadBtnAction.find("input.selected-files").val(selected);
  }

  /**
   * Fetch file data from server
   *
   * @param {string} url
   * @param {object|null} selected
   */
  function getAllData(url, selected = null)
  {
    var params = {};
    if (selected != null) {
      params["selected"] = selected;
    }
    if (fileSearchKey != null && fileSearchKey.length > 0) {
      params["search"] = fileSearchKey;
    }
    if (fileSortKey != null && fileSortKey.length > 0) {
      params["sort"] = fileSortKey;
    }
    else {
      params["sort"] = 'newest';
    }

    params["type"] = fileType;

    uploaderModal.find('.upload-content').html('')

    $.ajax({
      type: 'get',
      url: url,
      data: params,
      success: function (response)
      {
        uploaderModal.find('.upload-content').html(response.data)
        fmData.allFiles = response.links.data;
        addSelectedValue();
        if (response.links != '') {
          if (response.links.prev_page_url == null) {
            $('.uploader-prev-btn').addClass('disabled');
          } else {
            $('.uploader-prev-btn').removeClass('disabled');
            fmData.previousUrl = response.links.prev_page_url
          }
          if (response.links.next_page_url == null) {
            $('.uploader-next-btn').addClass('disabled');
          } else {
            $('.uploader-next-btn').removeClass('disabled');
            fmData.nextUrl = response.links.next_page_url
          }
        }
      }
    });
  }

  /**
   * Update already selected files to newly fetched file list
   */
  function addSelectedValue()
  {
    for (let i = 0; i < fmData.allFiles.length; i++) {
      let div = uploaderModal.find('.upload-content').find(`[data-value='${fmData.allFiles[i].id}']`)
      if (!fmData.selectedFiles.includes(fmData.allFiles[i].id)) {
        div.removeClass(activeFileClass);
      } else {
        div.addClass(activeFileClass);
      }
    }
    uploaderModal.find('.count-result').html(fmData.selectedFiles.length)
  }

  function parseBoolean(value)
  {
    return (value === 'true' || parseInt(value) > 0 || value === true || value === 'yes') ? true : false;
  }

  function prepareActionElement(element)
  {
    if (
      uploadBtnAction &&
      (uploadBtnAction.find('input.selected-files').attr('name') === element.find('input.selected-files').attr('name'))
    ) {
      return element;
    } else {

      fmData = clearFileSelectors();

      dataLoaded = false;

      $('.attachment-card.selected').removeClass(activeFileClass);

      return element;
    }
  }

  function clearFileSelectors()
  {
    return {
      selectedFiles: [],
      selectedFilesObject: [],
      allFiles: [],
      multiple: false,
      nextUrl: null,
      previousUrl: null
    };
  }

  /* --------------- END - Custom Functions Block ----------------- */

  /*
  *******************************************************************
  *************** File Drag and Drop Upload Script ******************
  *******************************************************************
  */
  let dropArea = document.getElementById("drop-area");
  let progressArray = []
  let progressBar = document.getElementById('uploader--progressBar')

    // Prevent default drag behaviors
    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName =>
    {
      dropArea.addEventListener(eventName, preventDefaults, false)
      document.body.addEventListener(eventName, preventDefaults, false)
    });

  // Highlight drop area when item is dragged over it
  ;['dragenter', 'dragover'].forEach(eventName =>
  {
    dropArea.addEventListener(eventName, highlight, false)
  });

  ;['dragleave', 'drop'].forEach(eventName =>
  {
    dropArea.addEventListener(eventName, unhighlight, false)
  });

  // Handle dropped files
  dropArea.addEventListener('drop', handleDrop, false)

  function preventDefaults(e)
  {
    e.preventDefault()
    e.stopPropagation()
  }

  function highlight(e)
  {
    dropArea.classList.add('highlight')
  }

  function unhighlight(e)
  {
    dropArea.classList.remove('active')
  }

  function handleDrop(e)
  {
    var dt = e.dataTransfer
    var files = dt.files

    handleFiles(files)
  }

  function initializeProgress(numFiles)
  {
    progressBar.value = 0
    progressArray = []

    for (let i = numFiles; i > 0; i--) {
      progressArray.push(0)
    }
  }

  function updateProgress(fileNumber, percent)
  {
    progressArray[fileNumber] = percent
    let total = progressArray.reduce((tot, curr) => tot + curr, 0) / progressArray.length
    progressBar.value = total
  }

  function handleFiles(files)
  {
    files = [...files]
    initializeProgress(files.length)
    files.forEach(uploadFile)
    files.forEach(previewFile)
  }

  $(function ()
  {
    $(document).on('change', '#drop-area input.upload-files', (event) =>
    {
      let files = $(this).find('input[type=file]')[0].files
      handleFiles(files)
      // console.log($(this).find('input[type=file]')[0].files)
    });

    $(document).on('click', '#drop-area .button', function ()
    {
      $('#drop-area input.upload-files').trigger('click');
    })

  });

  function previewFile(file)
  {
    let img = document.createElement('img')
    if (fileType === 'image') {
      let reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onloadend = function ()
      {
        img.src = reader.result

      }
    } else {
      img.src = baseUrl + '/media-manager/image/' + fileType;
    }
    document.getElementById('uploadedFileGallery').appendChild(img);
  }

  function uploadFile(file, i)
  {
    const url = $('#fileUploadUrl').length > 0 ? $('#fileUploadUrl').val() : (baseUrl + '/upload');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let xhr = new XMLHttpRequest()
    let formData = new FormData()
    xhr.open('POST', url, true)
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    xhr.setRequestHeader('X-CSRF-TOKEN', csrf)

    // Update progress (can be used to show progress indicator)
    xhr.upload.addEventListener("progress", function (e)
    {
      updateProgress(i, (e.loaded * 100.0 / e.total) || 100)
    })

    xhr.addEventListener('readystatechange', function (e)
    {
      if (xhr.readyState == 4 && xhr.status == 200) {
        updateProgress(i, 100) // <- Add this
        let response = JSON.parse(xhr.response)

        uploaderModal.find('.upload-content').html(response.data)
        fmData.allFiles = response.links.data;
        addSelectedValue();
      }
      else if (xhr.readyState == 4 && xhr.status != 200) {
        // Error. Inform the user
        console.log('upload error! check server ...')
      }
    })

    formData.append('file', file);

    formData.append('type', fileType);

    if (fileSearchKey != null && fileSearchKey.length > 0) {
      formData.append('search', fileSearchKey);
    }
    if (fileSortKey != null && fileSortKey.length > 0) {
      formData.append('sort', fileSortKey);
    } else {
      formData.append('sort', 'newest');
    }

    xhr.send(formData)
  }

  // ----------------- END - File Drag and Drop Upload Script --------------------

})(jQuery);
