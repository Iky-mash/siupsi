<main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200 dark:bg-slate-900">
    <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start" navbar-main navbar-scroll="true">
        <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
            <nav>
                <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
                    <li class="leading-normal text-sm">
                        <a class="opacity-50 text-slate-700 dark:text-slate-400" href="javascript:;">Pages</a>
                    </li>
                    <li class="text-sm pl-2 capitalize leading-normal text-slate-700 dark:text-white before:float-left before:pr-2 before:text-gray-600 dark:before:text-gray-400 before:content-['/']" aria-current="page">
                        <?= isset($title) ? $title : 'Page' ?>
                    </li>
                </ol>
                <h6 class="mb-0 font-bold capitalize text-slate-700 dark:text-white"><?= isset($title) ? $title : 'Page' ?></h6>
            </nav>

            <div class="flex items-center mt-2 grow sm:mt-0 md:ml-auto lg:flex lg:basis-auto">
                <ul class="flex flex-row items-center justify-end w-full pl-0 mb-0 list-none">
                    <li class="flex items-center px-2 md:px-4 relative"> 
                        <a href="javascript:;" class="block p-0 transition-all text-sm ease-nav-brand text-slate-500 dark:text-slate-300 hover:text-blue-500 dark:hover:text-blue-400" id="notification-dropdown-trigger" aria-expanded="false">
                            <i class="cursor-pointer fa fa-bell text-lg"></i>
                        </a>
                        <ul id="notification-dropdown-menu" class="text-sm transform-dropdown before:font-awesome before:leading-default before:duration-350 before:ease-soft lg:shadow-soft-3xl duration-250 min-w-44 before:sm:right-7.5 before:text-5.5 pointer-events-none absolute right-0 top-full mt-2 z-50 origin-top-right list-none rounded-lg border-0 border-solid border-transparent bg-white dark:bg-slate-800 dark:border-slate-700 shadow-xl dark:shadow-slate-700/40 px-2 py-4 text-left text-slate-500 dark:text-slate-300 opacity-0 transition-all before:absolute before:right-2 before:left-auto before:-top-5 before:z-50 before:inline-block before:font-normal before:text-white dark:before:text-slate-800 before:antialiased before:transition-all before:content-['\f0d8'] sm:-mr-6 lg:absolute lg:right-0 lg:left-auto lg:mt-2 lg:block lg:cursor-pointer hidden">
                           
                            <li class="relative mb-2">
                                <a class="ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent dark:hover:bg-slate-700 px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 dark:hover:text-white lg:transition-colors" href="javascript:;">
                                    <div class="flex py-1">
                                        <div class="my-auto">
                                            <img src="<?= base_url() ?>assets/img/team-2.jpg" class="inline-flex items-center justify-center mr-4 text-white text-sm h-9 w-9 max-w-none rounded-xl" />
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-1 font-normal leading-normal text-sm dark:text-white">
                                                <span class="font-semibold">New message</span> from Laur
                                            </h6>
                                            <p class="mb-0 leading-tight text-xs text-slate-400 dark:text-slate-500">
                                                <i class="mr-1 fa fa-clock"></i> 13 minutes ago
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                          
                        </ul>
                    </li>

                    <li class="flex items-center pl-2 pr-1 xl:hidden relative">
                        <button id="hamburger-button" aria-label="Open Menu" class="p-1.5 transition-colors duration-150 ease-in-out rounded-md text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 dark:focus:ring-offset-slate-900">
                            <div class="w-5 h-5"> 
                                <span class="sr-only">Open main menu</span>
                                <svg class="w-full h-full" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </div>
                        </button>
                        <div id="mobile-dropdown-menu"
                             class="absolute top-0 right-full mr-2 mt-1 w-64 origin-top-right rounded-xl bg-white dark:bg-slate-800 py-2 shadow-2xl ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 z-[99999] hidden opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out transform">
                        </div>
                    </li>

                    <li class="flex items-center pl-2 pr-1 md:px-2">
                        <a href="javascript:;" class="block p-0 transition-all text-sm ease-nav-brand text-slate-700 dark:text-slate-200" aria-expanded="false">
                            <i class="fa fa-user sm:mr-1.5 text-slate-600 dark:text-slate-300"></i>
                            <?php $user = $this->session->userdata(); ?>
                            <span class="hidden sm:inline font-medium"><?= isset($user['nama']) ? $user['nama'] : 'Guest' ?></span>
                        </a>
                    </li>
                    <li class="flex items-center pl-1 pr-0 md:px-2">
                        <form action="<?= site_url('auth/logout'); ?>" method="POST" style="display:inline;">
                            <button type="submit" title="Logout" class="flex items-center p-0 transition-colors duration-150 ease-in-out text-sm text-slate-600 dark:text-slate-300 hover:text-red-500 dark:hover:text-red-400 focus:outline-none bg-transparent border-none cursor-pointer">
                                <i class="fa fa-sign-out-alt sm:mr-1.5 text-lg"></i>
                                <span class="hidden sm:inline font-medium">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    

  
