$(function () {
  $('#check_reservation-form').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      data: $(this).serialize(),
      success: function (data) {
        if (data != '') {
          $('.availablity').append(
            '<div>Votre réservation au nom de ' +
              data.surname +
              ' ' +
              data.name +
              ' à' +
              date.hour +
              ' a bien été enregistrée. Au plaisir de vous voir !</div>'
          );
        } else {
          $('.availablity').append(
            "<div>Aucune réservation à ce nom n'a été trouvée</div>"
          );
        }
      },
      error: function () {},
    });
  });

  $('#new_reservation-form').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      data: $(this).serialize(),
      datatype: "JSON",
      success: function (data) {
        console.log(data);
        if (data != '') {
          let availablity = data;
          availablity.forEach(hour => {
            $('.availablity').append(
              '<div class="hour">' + hour + '</div>'
            );
          })
        } else {
        $('.availablity').append(
          '<div class="hour">Aucune disponibilité. Veuillez réessayer sur un autre jour.</div>'
        );
      }
    },
      error: function () {
        alert('Erreur');
      },
    });
  });
});
