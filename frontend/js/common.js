/**
 * QMore Checkout Seamless Demo
 * - Terms of use can be found under
 * https://guides.qenta.com/prerequisites
 * - License can be found under:
 * https://github.com/qenta-cee/qcs-example-php/blob/master/LICENSE.
 */

function reinitPage() {
  window.scrollTo(0, 0);
  window.location.reload();
}

function loadDataStorage(e) {
  var popupContent = document.querySelector('#ds > .popup > .content');
  var request = new XMLHttpRequest();
  request.responseType = 'document';
  request.open('GET', 'frontend/read_datastorage.php', true);
  request.onreadystatechange = function () {
    if (request.readyState === 4) {
      if (request.status !== 404) {
        popupContent.innerHTML = request.responseXML.querySelector('#main').innerHTML;
      }
    }
  };
  request.send();
}

function loadConfirmInfo(e) {
  var popupContent = document.querySelector('#ci > .popup > .content > pre');
  var request = new XMLHttpRequest();
  request.responseType = 'text';
  request.open('GET', 'frontend/confirm_info.txt', true);
  request.onreadystatechange = function () {
    if (request.readyState === 4) {
      if (request.status !== 404) {
        popupContent.innerHTML = request.responseText;
      }
    }
  };
  request.send();
}


function toggleDataStoragePopup() {
  if (document.getElementById('ds').classList.contains('open') === false) {
    loadDataStorage();
  }
  document.getElementById('ds').classList.toggle('open');
}

function toggleConfirmInfoPopup() {
  if (document.getElementById('ci').classList.contains('open') === false) {
    loadConfirmInfo();
  }
  document.getElementById('ci').classList.toggle('open');
}

function addButtonEvents() {
  const btnReadDataStorage = document.getElementById('btnReadDataStorage');
  if (btnReadDataStorage !== undefined) {
    btnReadDataStorage.addEventListener('click', toggleDataStoragePopup);
  }

  const btnShowConfirmationResult = document.getElementById('btnShowConfirmationResult');
  if (btnShowConfirmationResult !== undefined) {
    btnShowConfirmationResult.addEventListener('click', toggleConfirmInfoPopup);
  }


  const btnCloseDS = document.getElementById('btnCloseDS');
  if (btnCloseDS !== undefined) {
    btnCloseDS.addEventListener('click', toggleDataStoragePopup);
  }

  const btnCloseCI = document.getElementById('btnCloseCI');
  if (btnCloseCI !== undefined) {
    btnCloseCI.addEventListener('click', toggleConfirmInfoPopup);
  }

  const storeButtons = document.querySelectorAll('#tblStep2 input[onclick^="storeData"]');
  storeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      setTimeout(() => {
        document.getElementById('hStep3').scrollIntoView({behavior: "smooth"});
      }, 1000);
    });
  });

  const btnReinitPage = document.getElementById('btnReinitPage');
  if (btnReinitPage !== undefined) {
    btnReinitPage.addEventListener('click', reinitPage);
  }
}

document.addEventListener('readystatechange', event => {
  if (event.target.readyState === 'interactive') {
    addButtonEvents();
  }
});

window.onbeforeunload = () => {
  window.scrollTo(0, 0);
}
