<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    />
    <link rel="stylesheet" href="asset/css/style.css" />
    <title>Login Page</title>
  </head>

  <body class="login-page">
    <div
      class="container d-flex justify-content-center align-items-center min-vh-100"
    >
      <div class="row border rounded-5 p-3 bg-white shadow box-area">
        
        <!-- Kiri -->
        <div
          class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box"
          style="background: #5e3a03"
        >
          <div class="image mb-3">
            <img
              src="asset/img/logo_login.svg"
              class="img-fluid"
              style="width: 250px"
            />
          </div>

          <p
            class="text-white fs-2"
            style="font-family: 'Courier New', Courier, monospace"
          >
            Matrial Point
          </p>

          <small
            class="text-white text-wrap text-center"
            style="
              width: 17rem;
              font-family: 'Courier New', Courier, monospace;
            "
          >
            Melayani dengan sepenuh hati, memberikan solusi terbaik untuk kebutuhan Anda.
          </small>
        </div>

        <!-- Kanan -->
        <div class="col-md-6 right-box">
          <div class="row align-items-center">

            <div class="header-text mb-4">
              <h2>Selamat datang</h2>
              <p>Semangat, Berjuang, Sukses</p>
            </div>

            <!-- Alert -->
            <?php if(isset($_GET['message'])) : ?>
              <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['message']); ?>
              </div>
            <?php endif; ?>

            <!-- Form login -->
            <form method="POST" action="proses_login.php">

              <div class="input-group mb-3">
                <input
                  type="text"
                  name="username"
                  class="form-control form-control-lg bg-light fs-6"
                  placeholder="Username"
                  required
                />
              </div>

              <div class="input-group mb-1">
                <input
                  type="password"
                  name="password"
                  class="form-control form-control-lg bg-light fs-6"
                  placeholder="Password"
                  required
                />
              </div>

              <div class="input-group mb-5 d-flex justify-content-between">
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    id="formCheck"
                  />

                  <label
                    for="formCheck"
                    class="form-check-label text-secondary"
                  >
                    <small>Remember Me</small>
                  </label>
                </div>
              </div>

              <div class="input-group mb-3">
                <button type="submit" class="btn btn-lg btn-brown w-100 fs-6">
                  Login
                </button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </body>
</html>