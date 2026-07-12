<?php
include('includes/auth.php');
require_once __DIR__ . '/../includes/appointment_helpers.php';
$pageTitle = 'Book Appointment';
$uid = intval($_SESSION['bpmsuid']);
$cust = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM tblcustomers WHERE ID='$uid'"));

$stylistMap = [];
$servicesList = [];
$fallbackStylists = [];
$fsRet = mysqli_query($con, "SELECT ID, StylistName, Specialty FROM tblstylists ORDER BY StylistName");
while ($st = mysqli_fetch_array($fsRet)) {
    $fallbackStylists[] = [
        'id' => intval($st['ID']),
        'name' => $st['StylistName'],
        'specialty' => $st['Specialty'] ?? '',
    ];
}
$srvList = mysqli_query($con, "SELECT ID, ServiceName, Cost FROM tblservices ORDER BY ServiceName");
while ($s = mysqli_fetch_array($srvList)) {
    $servicesList[] = $s;
    $experts = msms_stylists_for_service($con, $s['ServiceName']);
    $stylistMap[$s['ServiceName']] = [];
    if (count($experts) === 0) {
        $stylistMap[$s['ServiceName']] = $fallbackStylists;
    } else {
        foreach ($experts as $ex) {
            $stylistMap[$s['ServiceName']][] = [
                'id' => intval($ex['ID']),
                'name' => $ex['StylistName'],
                'specialty' => $ex['Specialty'] ?? '',
            ];
        }
    }
}

if (isset($_POST['book'])) {
    $aptnum = strval(rand(100000000, 999999999));
    $name = mysqli_real_escape_string($con, $cust['Name']);
    $email = mysqli_real_escape_string($con, $cust['Email']);
    $phone = $cust['MobileNumber'];
    $aptdate = mysqli_real_escape_string($con, $_POST['aptdate']);
    $apttime = mysqli_real_escape_string($con, $_POST['apttime']);
    $service = mysqli_real_escape_string($con, $_POST['service']);
    $stylistId = intval($_POST['stylist_id'] ?? 0);

    if ($stylistId <= 0) {
        echo "<script>alert('Please select a stylist for this service.');</script>";
    } else {
        $allowed = false;
        foreach ($stylistMap[$_POST['service']] ?? [] as $ex) {
            if (intval($ex['id']) === $stylistId) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            echo "<script>alert('Invalid stylist for this service.');</script>";
        } else {
            $stylistSql = "'$stylistId'";
            $insert = build_appointment_insert($con, $aptnum, $name, $email, $phone, $aptdate, $apttime, $service, $stylistSql);
            if ($insert) {
                echo "<script>alert('Appointment requested! Apt #: $aptnum. Admin and your stylist will confirm.'); window.location='my-appointments.php';</script>";
            } else {
                echo "<script>alert('Booking failed. Please run SQL File/stylist_booking_update.sql on your database.');</script>";
            }
        }
    }
}

function build_appointment_insert($con, $aptnum, $name, $email, $phone, $aptdate, $apttime, $service, $stylistSql)
{
    if (msms_has_column($con, 'tblappointment', 'StylistStatus')) {
        $q = mysqli_query($con, "INSERT INTO tblappointment(AptNumber,Name,Email,PhoneNumber,AptDate,AptTime,Services,Remark,Status,StylistId,StylistStatus) VALUES('$aptnum','$name','$email','$phone','$aptdate','$apttime','$service','','', $stylistSql, '')");
    } elseif (msms_has_column($con, 'tblappointment', 'StylistId')) {
        $q = mysqli_query($con, "INSERT INTO tblappointment(AptNumber,Name,Email,PhoneNumber,AptDate,AptTime,Services,Remark,Status,StylistId) VALUES('$aptnum','$name','$email','$phone','$aptdate','$apttime','$service','','', $stylistSql)");
    } else {
        $q = mysqli_query($con, "INSERT INTO tblappointment(AptNumber,Name,Email,PhoneNumber,AptDate,AptTime,Services,Remark,Status) VALUES('$aptnum','$name','$email','$phone','$aptdate','$apttime','$service','','')");
    }
    return $q;
}

$preStylist = intval($_GET['stylist'] ?? 0);
include('includes/header.php');
?>
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="card-panel">
      <h3>Book New Appointment</h3>
      <p class="text-muted">Choose service, your preferred stylist (expert for that service), date and time. Admin and stylist both review your request.</p>
      <form method="post" id="bookForm">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Name</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($cust['Name']); ?>" readonly></div>
          <div class="col-md-6"><label class="form-label">Phone</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($cust['MobileNumber']); ?>" readonly></div>
          <div class="col-md-12">
            <label class="form-label">Service</label>
            <select name="service" id="serviceSelect" class="form-select" required>
              <option value="">Select service</option>
              <?php foreach ($servicesList as $s) {
                  echo '<option value="'.htmlspecialchars($s['ServiceName']).'">'.htmlspecialchars($s['ServiceName']).' — ₹'.intval($s['Cost']).'</option>';
              } ?>
            </select>
          </div>
          <div class="col-md-12">
            <label class="form-label">Choose stylist <span class="text-danger">*</span></label>
            <select name="stylist_id" id="stylistSelect" class="form-select" required>
              <option value="">— Select service first —</option>
            </select>
            <div id="stylistHint" class="form-text">Only stylists expert in the selected service are shown.</div>
          </div>
          <div class="col-md-6"><label class="form-label">Preferred date</label><input type="date" name="aptdate" class="form-control" min="<?php echo date('Y-m-d'); ?>" required></div>
          <div class="col-md-6"><label class="form-label">Preferred time</label><input type="time" name="apttime" class="form-control" required></div>
        </div>
        <button type="submit" name="book" class="btn btn-salon mt-4">Submit Request</button>
        <a href="stylists.php" class="btn btn-outline-secondary mt-4 ms-2">Browse stylists</a>
      </form>
    </div>
  </div>
</div>
<script>
const stylistByService = <?php echo json_encode($stylistMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const preStylistId = <?php echo $preStylist; ?>;

function refreshStylists() {
  const svc = document.getElementById('serviceSelect').value;
  const sel = document.getElementById('stylistSelect');
  sel.innerHTML = '';
  const list = stylistByService[svc] || [];
  if (!svc) {
    sel.innerHTML = '<option value="">— Select service first —</option>';
    return;
  }
  if (list.length === 0) {
    sel.innerHTML = '<option value="">No stylist listed for this service — contact salon</option>';
    return;
  }
  sel.innerHTML = '<option value="">— Choose stylist —</option>';
  list.forEach(function (st) {
    const opt = document.createElement('option');
    opt.value = st.id;
    opt.textContent = st.name + (st.specialty ? ' — ' + st.specialty : '');
    if (preStylistId && st.id === preStylistId) opt.selected = true;
    sel.appendChild(opt);
  });
}

document.getElementById('serviceSelect').addEventListener('change', refreshStylists);
if (preStylistId) {
  for (const svc in stylistByService) {
    if (stylistByService[svc].some(function (st) { return st.id === preStylistId; })) {
      document.getElementById('serviceSelect').value = svc;
      break;
    }
  }
}
refreshStylists();
</script>
<?php include('includes/footer.php'); ?>
