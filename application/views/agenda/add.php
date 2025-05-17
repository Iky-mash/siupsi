<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Dosen</title>

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">

    <!-- jQuery & FullCalendar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    <style>
        #slot-container {
            display: none;
            margin-bottom: 20px;
        }
        #slot-list label {
            display: block;
            margin: 5px 0;
        }
        #calendar {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Tambah Agenda Dosen</h2>

    <!-- Slot muncul di atas kalender -->
    <div id="slot-container">
        <h3>Slot Waktu Tersedia</h3>
        <form id="slot-form">
            <input type="hidden" id="selected-date" name="tanggal">
            <div id="slot-list"></div>
            <button type="submit">Simpan</button>
        </form>
    </div>

    <!-- Kalender ada di bawah slot -->
    <div id="calendar"></div>

    <script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            selectable: true,
            select: function(start) {
                let selectedDate = start.format("YYYY-MM-DD");
                let dayOfWeek = start.day(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu
                
                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    alert("❌ Hanya bisa memilih hari Senin-Jumat!");
                    return;
                }

                $("#selected-date").val(selectedDate);
                $("#slot-container").show();
                loadSlots();
            }
        });

        function loadSlots() {
            let slots = [
                { id: 1, waktu: "08:45-10:25" },
                { id: 2, waktu: "10:30-12:10" },
                { id: 3, waktu: "13:00-14:40" },
                { id: 4, waktu: "14:45-16:25" }
            ];

            let slotList = $("#slot-list");
            slotList.empty();
            
            slots.forEach(slot => {
                slotList.append(`
                    <label>
                        <input type="checkbox" name="slot_waktu[]" value="${slot.waktu}">
                        ${slot.waktu}
                    </label>
                `);
            });
        }

        $("#slot-form").on("submit", function(e) {
            e.preventDefault();
            
            let selectedDate = $("#selected-date").val();
            let selectedSlots = [];
            
            $("input[name='slot_waktu[]']:checked").each(function() {
                selectedSlots.push($(this).val());
            });

            if (selectedSlots.length === 0) {
                alert("❌ Pilih minimal 1 slot waktu!");
                return;
            }

            $.ajax({
                url: "http://localhost/siupsi/agenda/simpan_slot",
                type: "POST",
                data: { 
                    tanggal: selectedDate, 
                    slot_waktu: selectedSlots.join(',') // Ubah array menjadi string
                },
                success: function(response) {
                    alert("✅ Slot berhasil disimpan!");
                    $("#slot-container").hide();
                },
                error: function(xhr, status, error) {
                    alert("❌ Gagal menyimpan data: " + error);
                }
            });
        });
    });
    </script>
</body>
</html>
