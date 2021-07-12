<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет по транзакциям</title>
    <link href="/css/app.css" rel="stylesheet">
    <script src="/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            searchBottom = document.getElementById("search");
            searchBottom.addEventListener("click", function () {
                let dateFrom = document.getElementById("date_from").value;
                let dateTo = document.getElementById("date_to").value;
                let name = document.getElementById("name").value;
                let country = document.getElementById("country").value;
                let cityOfRegistration = document.getElementById("city_of_registration").value;
                if (!name || !country || !cityOfRegistration) {
                    alert('Поля Имя/Страна/Город регистрации обязательны для заполнения');
                    return;
                }
                axios({
                    method: 'post',
                    url: '/reports/transaction/getData',
                    data: {
                        date_from: dateFrom,
                        date_to: dateTo,
                        name: name,
                        country: country,
                        city_of_registration: cityOfRegistration
                    },
                }).then(
                    function (response) {
                        let currentSum = document.getElementById("currentSum");
                        currentSum.textContent = `Общая сумма операций: ${response.data.currentSum}`;
                        let usdSum = document.getElementById("usdSum");
                        usdSum.textContent = `Общая сумма операций в USD: ${response.data.usdSum}`;
                        let tableData = response.data.data;
                        appendRow(tableData);
                    }
                ).catch(function (error) {
                    alert('Произошла ошибка');
                    return;
                });
            });

            downloadBottom = document.getElementById("download");
            downloadBottom.addEventListener("click", function () {
                let dateFrom = document.getElementById("date_from").value;
                let dateTo = document.getElementById("date_to").value;
                let name = document.getElementById("name").value;
                let country = document.getElementById("country").value;
                let cityOfRegistration = document.getElementById("city_of_registration").value;
                if (!name || !country || !cityOfRegistration) {
                    alert('Поля Имя/Страна/Город регистрации обязательны для заполнения');
                    return;
                }

                axios({
                    url: '/reports/transaction/exportcsv',
                    method: 'post',
                    data: {
                        date_from: dateFrom,
                        date_to: dateTo,
                        name: name,
                        country: country,
                        city_of_registration: cityOfRegistration
                    },
                    responseType: 'blob', // important
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'file.scv'); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                });
            });

            function appendRow(tableData) {
                let tableBody = document.getElementById("main_body");
                tableBody.innerHTML = '';
                tableData.forEach((element, index) => {

                    let newRow = document.createElement("tr");
                    newRow.setAttribute("scope", "row");

                    let newCell = document.createElement("th");
                    newCell.textContent = index + 1;
                    newRow.appendChild(newCell);

                    for (let key in element) {
                        let newCell = document.createElement("td");
                        if (key == 'value')
                            newCell.textContent = element[key].toFixed(2);
                        else
                            newCell.textContent = element[key];
                        newRow.appendChild(newCell);
                    }
                    tableBody.appendChild(newRow);
                });
            };
        });
    </script>
</head>
<body>
<div class="col-8 offset-2 h-75 mt-5" style="background: white">
    <div class="row p-3">
        <div class="mb-3 col-3">
            <label for="exampleFormControlInput1" class="form-label">Имя</label>
            <input type="text" class="form-control" id="name" placeholder="Иванов Иван">
        </div>
        <div class="mb-3 col-3">
            <label for="exampleFormControlInput1" class="form-label">Страна</label>
            <input type="text" class="form-control" id="country" placeholder="Россия">
        </div>
        <div class="mb-3 col-3">
            <label for="exampleFormControlInput1" class="form-label">Город регистрации</label>
            <input type="text" class="form-control" id="city_of_registration" placeholder="Москва">
        </div>
    </div>
    <div class="input-group mb-3 p-3">
        <span class="input-group-text">c</span>
        <input type="date" class="form-control col-2" id="date_from" name="date" placeholder="Дата" requiredl>
        <span class="input-group-text">по</span>
        <input type="date" class="form-control col-2" id="date_to" name="date" placeholder="Дата" required>
        <button type="button" class="btn btn-primary ml-4" id="search">Показать отчет</button>
        <button type="button" class="btn btn-primary ml-4" id="download">Скачать отчет в формате CSV</button>
    </div>


    <div><h3>Список транзакций</h3></div>
    <div class="row p-3">
        <table class="table col-8">
            <thead class="table-light">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Дата операции</th>
                <th scope="col">Изменение баланса</th>
                <th scope="col">Тип операции</th>
            </tr>
            </thead>
            <tbody id="main_body">
            </tbody>
            <tfoot>
            </tfoot>
        </table>
        <div class="col-4">
            <div id="currentSum"></div>
            <div id="usdSum"></div>
        </div>
    </div>
</div>
</body>
</html>
