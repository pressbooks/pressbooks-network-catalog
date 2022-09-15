import dayjs from "dayjs";
import flatpickr from "flatpickr";

export default function PbDatePicker() {

  window.formatDate = function(date) {
    return date !== 'DD/MM/YYYY' && date!=='' ? dayjs(date).format('DD/MM/YYYY') : 'DD/MM/YYYY';
  }

  flatpickr.defaultConfig.nextArrow = '<svg xmlns="https://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">\n' +
    '  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />\n' +
    '</svg>\n';

  flatpickr.defaultConfig.prevArrow = '<svg xmlns="https://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">\n' +
    '  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />\n' +
    '</svg>\n';


  const fromElement = document.getElementById('updated_from');
  const toElement = document.getElementById('updated_to');

  flatpickr(fromElement, {inline: true, dateFormat: 'Y-m-d', defaultDate: fromElement.value});
  flatpickr(toElement, {inline: true, dateFormat: 'Y-m-d', defaultDate: toElement.value});

}
