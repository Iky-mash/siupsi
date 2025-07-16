<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link
		rel="apple-touch-icon"
		sizes="76x76"
		href="<?= base_url() ?>assets/img/apple-icon.png" />
	<link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png" />
	    <title>Login Siupsi</title>
	<!--     Fonts and icons     -->
	<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	<link
		href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
		rel="stylesheet" />
	<!-- Font Awesome Icons -->
	<script
		src="https://kit.fontawesome.com/42d5adcbca.js"
		crossorigin="anonymous"></script>
	<!-- Nucleo Icons -->
	<link href="<?= base_url() ?>assets/css/nucleo-icons.css" rel="stylesheet" />
	<link href="<?= base_url() ?>assets/css/nucleo-svg.css" rel="stylesheet" />
	<!-- Popper -->
	<script src="https://unpkg.com/@popperjs/core@2"></script>
	<!-- Main Styling -->
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	<link
		href="<?= base_url() ?>assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5"
		rel="stylesheet" />
		<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

	<!-- Nepcha Analytics (nepcha.com) -->
	<!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
	<script
		defer
		data-site="YOUR_DOMAIN_HERE"
		src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
</head>

  <body class="m-0 font-sans antialiased font-normal bg-white text-start text-base leading-default text-slate-500">
    
    <main class="mt-0 transition-all duration-200 ease-soft-in-out">
      <section class="min-h-screen mb-32">
        <div class="relative flex items-start pt-12 pb-56 m-4 overflow-hidden bg-center bg-cover min-h-50-screen rounded-xl" style="background-image: url('<?= base_url() ?>assets/img/curved-images/curved14.jpg')">
          <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 opacity-60"></span>
          <div class="container z-10">
            <div class="flex flex-wrap justify-center -mx-3">
              <div class="w-full max-w-full px-3 mx-auto mt-0 text-center lg:flex-0 shrink-0 lg:w-5/12">
                <h1 class="mt-12 mb-2 text-white">Selamat Datang di SIUPSI!</h1> <p class="text-white"> Solusi cerdas untuk mengelola penjadwalan ujian skripsi dengan mudah, efisien, dan terorganisir!</p> </div>
            </div>
          </div>
        </div>

        <div class="container">
          <div class="flex flex-wrap -mx-3 -mt-48 md:-mt-56 lg:-mt-48">
            <div class="w-full max-w-full px-3 mx-auto mt-0 md:flex-0 shrink-0 md:w-7/12 lg:w-5/12 xl:w-4/12">
              <div class="relative z-0 flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                
                <div class="p-6 mb-0 text-center bg-white border-b-0 rounded-t-2xl">
                  <h5>Login dengan</h5> </div>
              
                <div class="flex-auto p-6">
                  <?php if ($this->session->flashdata('login_error_message')): ?>
                  <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center" role="alert">
                    <span class="block sm:inline"><?= $this->session->flashdata('login_error_message'); ?></span>
                  </div>
                  <?php endif; ?>

                  <?php if ($this->session->flashdata('login_success_message')): ?>
                  <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-center" role="alert">
                    <span class="block sm:inline"><?= $this->session->flashdata('login_success_message'); ?></span>
                  </div>
                  <?php endif; ?>
                  <form role="form" method="post" action="<?= base_url('auth');?>" >
                      <label class="mb-2 ml-1 font-bold text-xs text-slate-700">Email</label>
                      <div class="mb-4">
                        <input type="text" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow" placeholder="Email" id="email" name="email" value="<?= set_value('email'); ?>" aria-describedby="email-addon" />
                        <?= form_error('email', ' <small class="text-red-500 text-sm pl-3">', '</small>'); ?>
                      </div>
                      <label class="mb-2 ml-1 font-bold text-xs text-slate-700">Password</label>
                      <div class="mb-4">
                        <input type="password" class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow" placeholder="Password" id="password" name="password" aria-describedby="password-addon" />
                        <?= form_error('password', ' <small class="text-red-500 text-sm pl-3">', '</small>'); ?>
                      </div>
                    
                      <div class="text-center">
                        <button type="submit" class="inline-block w-full px-6 py-3 mt-6 mb-0 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer shadow-soft-md bg-x-25 bg-150 leading-pro text-xs ease-soft-in tracking-tight-soft bg-gradient-to-tl from-blue-600 to-cyan-400 hover:scale-102 hover:shadow-soft-xs active:opacity-85">Masuk</button> </div>
                  </form>
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
  <script src="<?= base_url() ?>assets/js/plugins/perfect-scrollbar.min.js" async></script>
  <script src="<?= base_url() ?>assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5" async></script>
</html>