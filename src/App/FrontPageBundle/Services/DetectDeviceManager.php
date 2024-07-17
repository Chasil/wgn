<?php
/**
 * This file is part of the AppFrontPageBundle package.
 *
 */
namespace App\FrontPageBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Class DetectDeviceManager
 *
 * @author wojciech przygoda
 */
class DetectDeviceManager {


    /**
     *
     * @var Container services container
     */
   private $services;

   /**
    *
    * @var string user agent
    */
   private $useragent = "";

   /**
    *
    * @var string http accept
    */
   private $httpaccept = "";
   /**
    *
    * @var int true
    */
   private $true = 1;

   /**
    *
    * @var flase false
    */
   private $false = 0;
   /**
    *
    * @var int Stores whether we're currently initializing the most popular functions.
    */
   private $initCompleted = 0;
   /**
    *
    * @var int Stores the result of DetectWebkit()
    */
   private $isWebkit = 0;

   /**
    *
    * @var int Stores the result of DetectMobileQuick()
    */
   private $isMobilePhone = 0;

   /**
    *
    * @var int Stores the result of DetectIphone()
    */
   private $isIphone = 0;

   /**
    *
    * @var int Stores the result of DetectAndroid()
    */
   private $isAndroid = 0;

   /**
    * @var int Stores the result of DetectAndroidPhone()
    */
   private $isAndroidPhone = 0;

   /**
    * @var int Stores the result of DetectTierTablet()
    */
   private $isTierTablet = 0;

   /**
    *
    * @var int Stores the result of DetectTierIphone()
    */
   private $isTierIphone = 0;

   /**
    *
    * @var int Stores the result of DetectTierRichCss()
    */
   private $isTierRichCss = 0;

   /**
    *
    * @var int Stores the result of DetectTierOtherPhones()
    */
   private $isTierGenericMobile = 0;

   /**
    *
    * @var string webkit
    */
   private $engineWebKit = 'webkit';

   /**
    *
    * @var string iphone
    */
   private $deviceIphone = 'iphone';

   /**
    *
    * @var string ipod
    */
   private $deviceIpod = 'ipod';

   /**
    *
    * @var string ipad
    */
   private $deviceIpad = 'ipad';

   /**
    *
    * @var string macintosh used for disambiguation
    */
   private $deviceMacPpc = 'macintosh';

   /**
    *
    * @var string android
    */
   private $deviceAndroid = 'android';

   /**
    *
    * @var string googletv
    */
   private $deviceGoogleTV = 'googletv';

   /**
    *
    * @var string windows phone os 7
    */
   private $deviceWinPhone7 = 'windows phone os 7';

   /**
    *
    * @var string windows phone 8
    */
   private $deviceWinPhone8 = 'windows phone 8';

   /**
    *
    * @var string windows phone 10
    */
   private $deviceWinPhone10 = 'windows phone 10';

   /**
    *
    * @var string windows ce
    */
   private $deviceWinMob = 'windows ce';

   /**
    *
    * @var string windows
    */
   private $deviceWindows = 'windows';

   /**
    *
    * @var string  iemobile
    */
   private $deviceIeMob = 'iemobile';

   /**
    *
    * @var string ppc - stands for PocketPC
    */
   private $devicePpc = 'ppc';

   /**
    *
    * @var string wm5 pie -  An old Windows Mobile
    */
   private $enginePie = 'wm5 pie';

   /**
    *
    * @var string blackberry
    */
   private $deviceBB = 'blackberry';

   /**
    *
    * @var string bb10 -  For the new BB 10 OS
    */
   private $deviceBB10 = 'bb10';

   /**
    *
    * @var string vnd.rim -  Detectable when BB devices emulate IE or Firefox
    */
   private $vndRIM = 'vnd.rim';

   /**
    *
    * @var string blackberry95 -  Storm 1 and 2
    */
   private $deviceBBStorm = 'blackberry95';

   /**
    *
    * @var string blackberry97 -  Bold 97x0 (non-touch)
    */
   private $deviceBBBold = 'blackberry97';

   /**
    *
    * @var string blackberry 99 -  Bold 99x0 (touchscreen)
    */
   private $deviceBBBoldTouch = 'blackberry 99';
   /**
    *
    * @var string blackberry96 -  Tour
    */
   private $deviceBBTour = 'blackberry96';

   /**
    *
    * @var string blackberry89 -  Curve2
    */
   private $deviceBBCurve = 'blackberry89';

   /**
    *
    * @var string blackberry 938 -  Curve Touch
    */
   private $deviceBBCurveTouch = 'blackberry 938';

   /**
    *
    * @var string blackberry 98 -  Torch
    */
   private $deviceBBTorch = 'blackberry 98';

   /**
    *
    * @var string playbook -  PlayBook tablet
    */
   private $deviceBBPlaybook = 'playbook';

   /**
    *
    * @var string symbian
    */
   private $deviceSymbian = 'symbian';

   /**
    *
    * @var string series60
    */
   private $deviceS60 = 'series60';

   /**
    *
    * @var string series70
    */
   private $deviceS70 = 'series70';

   /**
    *
    * @var string series80
    */
   private $deviceS80 = 'series80';

   /**
    *
    * @var string series90
    */
   private $deviceS90 = 'series90';

   /**
    *
    * @var string palm
    */
   private $devicePalm = 'palm';

   /**
    *
    * @var string webos - For Palm devices
    */
   private $deviceWebOS = 'webos';

   /**
    *
    * @var string web0s - For LG TVs
    */
   private $deviceWebOStv = 'web0s';

   /**
    *
    * @var string hpwos - For HP's line of WebOS devices
    */
   private $deviceWebOShp = 'hpwos';

   /**
    *
    * @var string nuvifone - Garmin Nuvifon
    */
   private $deviceNuvifone = 'nuvifone';

   /**
    *
    * @var string bada - Samsung's Bada OS
    */
   private $deviceBada = 'bada';

   /**
    *
    * @var string tizen - Tizen OS
    */
   private $deviceTizen = 'tizen';

   /**
    *
    * @var string meego - Meego OS
    */
   private $deviceMeego = 'meego';

   /**
    *
    * @var string sailfish - Sailfish OS
    */
   private $deviceSailfish = 'sailfish';

   /**
    *
    * @var string ubuntu - Ubuntu Mobile OS
    */
   private $deviceUbuntu = 'ubuntu';

   /**
    *
    * @var string kindle - Amazon Kindle, eInk one
    */
   private $deviceKindle = 'kindle';

   /**
    *
    * @var string silk-accelerated - Amazon's accelerated Silk browser for Kindle Fire
    */
   private $engineSilk = 'silk-accelerated'; //Amazon's accelerated Silk browser for Kindle Fire

   /**
    *
    * @var string blazer - Old Palm browser
    */
   private $engineBlazer = 'blazer';

   /**
    *
    * @var string xiino - Another old Palm
    */
   private $engineXiino = 'xiino';

   /**
    *
    * @var string vnd.wap
    */
   private $vndwap = 'vnd.wap';

   /**
    *
    * @var string wml
    */
   private $wml = 'wml';

   /**
    *
    * @var string tablet - Generic term for slate and tablet devices
    */
   private $deviceTablet = 'tablet';

   /**
    *
    * @var string brew
    */
   private $deviceBrew = 'brew';

   /**
    *
    * @var string danger
    */
   private $deviceDanger = 'danger';

   /**
    *
    * @var string hiptop
    */
   private $deviceHiptop = 'hiptop';

   /**
    *
    * @var string playstation
    */
   private $devicePlaystation = 'playstation';

   /**
    *
    * @var string vita
    */
   private $devicePlaystationVita = 'vita';

   /**
    *
    * @var string nitro
    */
   private $deviceNintendoDs = 'nitro';

   /**
    *
    * @var string nintendo
    */
   private $deviceNintendo = 'nintendo';

   /**
    *
    * @var string wii
    */
   private $deviceWii = 'wii';

   /**
    *
    * @var string xbox
    */
   private $deviceXbox = 'xbox';

   /**
    *
    * @var string archos
    */
   private $deviceArchos = 'archos';

   /**
    *
    * @var string firefox - For Firefox OS
    */
   private $engineFirefox = 'firefox';

   /**
    *
    * @var string opera - Popular browser
    */
   private $engineOpera = 'opera';

   /**
    *
    * @var string netfront - Common embedded OS browser
    */
   private $engineNetfront = 'netfront';

   /**
    *
    * @var string up.browser - common on some phones
    */
   private $engineUpBrowser = 'up.browser';

   /**
    *
    * @var string openweb - Transcoding by OpenWave server
    */
   private $engineOpenWeb = 'openweb';

   /**
    *
    * @var string midp - a mobile Java technology
    */
   private $deviceMidp = 'midp';

   /**
    *
    * @var string up.link
    */
   private $uplink = 'up.link';

   /**
    *
    * @var string teleca q - a modern feature phone browser
    */
   private $engineTelecaQ = 'teleca q';

   /**
    *
    * @var string pda - some devices report themselves as PDAs
    */
   private $devicePda = 'pda';

   /**
    *
    * @var string mini - Some mobile browsers put 'mini' in their names.
    */
   private $mini = 'mini';

   /**
    *
    * @var string mobile - Some mobile browsers put 'mobile' in their user agent strings.
    */
   private $mobile = 'mobile';

   /**
    *
    * @var string mobi - Some mobile browsers put 'mobi' in their user agent strings.
    */
   private $mobi = 'mobi';

   /**
    *
    * @var string smart-tv - Samsung Tizen smart TVs
    */
   private $smartTV1 = 'smart-tv';

   /**
    *
    * @var string smarttv - LG WebOS smart TVs
    */
   private $smartTV2 = 'smarttv';

   /**
    *
    * @var string maemo
    */
   private $maemo = 'maemo';

   /**
    *
    * @var string linux
    */
   private $linux = 'linux';

   /**
    *
    * @var string qt embedded - for Sony Mylo and others
    */
   private $qtembedded = 'qt embedded';

   /**
    *
    * @var string com2 - for Sony Mylo also
    */
   private $mylocom2 = 'com2';

   /**
    *
    * @var string sonyericsson
    */
   private $manuSonyEricsson = "sonyericsson";

   /**
    *
    * @var string brew
    */
   private $manuericsson = "ericsson";

   /**
    *
    * @var string sec-sgh
    */
   private $manuSamsung1 = "sec-sgh";

   /**
    *
    * @var string sony
    */
   private $manuSony = "sony";

   /**
    *
    * @var string htc - Popular Android and WinMo manufacturer
    */
   private $manuHtc = "htc";

   /**
    *
    * @var string docomo
    */
   private $svcDocomo = "docomo";

   /**
    *
    * @var string kddi
    */
   private $svcKddi = "kddi";

   /**
    *
    * @var string vodafone
    */
   private $svcVodafone = "vodafone";

   /**
    *
    * @var string update -  pda vs. update
    */
   private $disUpdate = "update";

    /**
     * Constructor
     *
     * @param Container $container services container
     */
    function __construct(Container $container) {
      $this->services = $container;
      $this->uagent_info();
    }

  /**
   * The object initializer. Initializes several default variables.
   */
   function uagent_info()
   {
	 $this->useragent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
	 $this->httpaccept = isset($_SERVER['HTTP_ACCEPT'])?strtolower($_SERVER['HTTP_ACCEPT']):'';

	 //Let's initialize some values to save cycles later.
	 $this->InitDeviceScan();
   }

   /**
    * Initialize Key Stored Values.
    */
   function InitDeviceScan()
   {
        //Save these properties to speed processing

        $this->isWebkit = $this->DetectWebkit();
        $this->isIphone = $this->DetectIphone();
        $this->isAndroid = $this->DetectAndroid();
        $this->isAndroidPhone = $this->DetectAndroidPhone();

        $this->isMobilePhone = $this->DetectMobileQuick();
        $this->isTierIphone = $this->DetectTierIphone();
        $this->isTierTablet = $this->DetectTierTablet();

        //Optional: Comment these out if you NEVER use them.

        $this->isTierRichCss = $this->DetectTierRichCss();
        $this->isTierGenericMobile = $this->DetectTierOtherPhones();

        $this->initCompleted = $this->true;
   }
   /**
    * Returns the contents of the User Agent value, in lower case.
    * @return string
    */
   function Get_Uagent()
   {
       return $this->useragent;
   }
   /**
    * Returns the contents of the HTTP Accept value, in lower case.
    * @return string
    */
   function Get_HttpAccept()
   {
       return $this->httpaccept;
   }

   /**
    * Detects if the current device is an iPhone.
    *
    * @return int
    */
   function DetectIphone()
   {
      if ($this->initCompleted == $this->true ||
          $this->isIphone == $this->true)
         return $this->isIphone;

      if (stripos($this->useragent, $this->deviceIphone) > -1)
      {
         //The iPad and iPod Touch say they're an iPhone. So let's disambiguate.
         if ($this->DetectIpad() == $this->true ||
             $this->DetectIpod() == $this->true)
            return $this->false;
         //Yay! It's an iPhone!
         else
            return $this->true;
      }
      else
         return $this->false;
   }
   /**
    * Detects if the current device is an iPod Touch.
    * @return int
    */
   function DetectIpod()
   {
      if (stripos($this->useragent, $this->deviceIpod) > -1)
         return $this->true;
      else
         return $this->false;
   }
   /**
    * Detects if the current device is an iPad tablet.
    * @return int
    */
   function DetectIpad()
   {
      if (stripos($this->useragent, $this->deviceIpad) > -1 &&
          $this->DetectWebkit() == $this->true)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device is an iPhone or iPod Touch.
    * @return int
    */
   function DetectIphoneOrIpod()
   {
       //We repeat the searches here because some iPods may report themselves as an iPhone, which would be okay.
      if ($this->DetectIphone() == $this->true ||
             $this->DetectIpod() == $this->true)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects *any* iOS device: iPhone, iPod Touch, iPad.
    * @return int
    */
   function DetectIos()
   {
      if (($this->DetectIphoneOrIpod() == $this->true) ||
        ($this->DetectIpad() == $this->true))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects *any* Android OS-based device: phone, tablet, and multi-media player.
    * Also detects Google TV.
    *
    * @return int
    */
   function DetectAndroid()
   {
      if ($this->initCompleted == $this->true ||
          $this->isAndroid == $this->true)
         return $this->isAndroid;
      if ((stripos($this->useragent, $this->deviceAndroid) > -1)
          || ($this->DetectGoogleTV() == $this->true))
         return $this->true;

      return $this->false;
   }
   //**************************
   // Detects if the current device is a (small-ish) Android OS-based device
   // used for calling and/or multi-media (like a Samsung Galaxy Player).
   // Google says these devices will have 'Android' AND 'mobile' in user agent.
   // Ignores tablets (Honeycomb and later).


   /**
    * Detects if the current device is a (small-ish) Android OS-based device
    * used for calling and/or multi-media (like a Samsung Galaxy Player).
    * Google says these devices will have 'Android' AND 'mobile' in user agent.
    * Ignores tablets (Honeycomb and later).
    *
    * @return int
    */
   function DetectAndroidPhone()
   {
      if ($this->initCompleted == $this->true ||
          $this->isAndroidPhone == $this->true)
         return $this->isAndroidPhone;

      //First, let's make sure we're on an Android device.
      if ($this->DetectAndroid() == $this->false)
         return $this->false;
	  //If it's Android and has 'mobile' in it, Google says it's a phone.
      if (stripos($this->useragent, $this->mobile) > -1)
         return $this->true;

      //Special check for Android devices with Opera Mobile/Mini. They should report here.
      if (($this->DetectOperaMobile() == $this->true))
         return $this->true;

      return $this->false;
   }

   /**
    * Detects if the current device is a (self-reported) Android tablet.
    * Google says these devices will have 'Android' and NOT 'mobile' in their user agent.
    *
    * @return int
    */
   function DetectAndroidTablet()
   {
      //First, let's make sure we're on an Android device.
      if ($this->DetectAndroid() == $this->false)
         return $this->false;
      //Special check for Android devices with Opera Mobile/Mini. They should NOT report here.
      if ($this->DetectOperaMobile() == $this->true)
         return $this->false;

      //Otherwise, if it's Android and does NOT have 'mobile' in it, Google says it's a tablet.
      if (stripos($this->useragent, $this->mobile) > -1)
         return $this->false;
      else
         return $this->true;
   }

   /**
    * Detects if the current device is an Android OS-based device and
    * the browser is based on WebKit.
    *
    * @return int
    */
   function DetectAndroidWebKit()
   {
      if (($this->DetectAndroid() == $this->true) &&
		($this->DetectWebkit() == $this->true))
         return $this->true;
      else
         return $this->false;
   }
   /**
    * Detects if the current device is a GoogleTV.
    *
    * @return int
    */
   function DetectGoogleTV()
   {
      if (stripos($this->useragent, $this->deviceGoogleTV) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is based on WebKit.
    *
    * @return int
    */
   function DetectWebkit()
   {
      if ($this->initCompleted == $this->true ||
          $this->isWebkit == $this->true)
         return $this->isWebkit;
      if (stripos($this->useragent, $this->engineWebKit) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is a Windows Phone 7, 8, or 10 device.
    *
    * @return int
    */
   function DetectWindowsPhone()
   {
      if (($this->DetectWindowsPhone7() == $this->true)
			|| ($this->DetectWindowsPhone8() == $this->true)
			|| ($this->DetectWindowsPhone10() == $this->true))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a Windows Phone 7 device (in mobile browsing mode).
    *
    * @return int
    */
   function DetectWindowsPhone7()
   {
      if (stripos($this->useragent, $this->deviceWinPhone7) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a Windows Phone 8 device (in mobile browsing mode).
    *
    * @return int
    */
   function DetectWindowsPhone8()
   {
      if (stripos($this->useragent, $this->deviceWinPhone8) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a Windows Phone 10 device (in mobile browsing mode).
    *
    * @return int
    */
   function DetectWindowsPhone10()
   {
      if (stripos($this->useragent, $this->deviceWinPhone10) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is a Windows Mobile device.
    * Excludes Windows Phone 7 and later devices.
    * Focuses on Windows Mobile 6.xx and earlier.
    *
    * @return int
    */
   function DetectWindowsMobile()
   {
      if ($this->DetectWindowsPhone() == $this->true)
         return $this->false;

      //Most devices use 'Windows CE', but some report 'iemobile'
      //  and some older ones report as 'PIE' for Pocket IE.
      if (stripos($this->useragent, $this->deviceWinMob) > -1 ||
          stripos($this->useragent, $this->deviceIeMob) > -1 ||
          stripos($this->useragent, $this->enginePie) > -1)
         return $this->true;
      //Test for Windows Mobile PPC but not old Macintosh PowerPC.
	  if (stripos($this->useragent, $this->devicePpc) > -1
		  && !(stripos($this->useragent, $this->deviceMacPpc) > 1))
         return $this->true;
      //Test for certain Windwos Mobile-based HTC devices.
      if (stripos($this->useragent, $this->manuHtc) > -1 &&
          stripos($this->useragent, $this->deviceWindows) > -1)
         return $this->true;
      if ($this->DetectWapWml() == $this->true &&
          stripos($this->useragent, $this->deviceWindows) > -1)
         return $this->true;
      else
         return $this->false;
   }
   /**
    * Detects if the current browser is any BlackBerry device.
    * Includes BB10 OS, but excludes the PlayBook.
    *
    * @return int
    */
   function DetectBlackBerry()
   {
       if ((stripos($this->useragent, $this->deviceBB) > -1) ||
          (stripos($this->httpaccept, $this->vndRIM) > -1))
         return $this->true;
      if ($this->DetectBlackBerry10Phone() == $this->true)
         return $this->true;
       else
         return $this->false;
   }

   /**
    * Detects if the current browser is a BlackBerry 10 OS phone.
    * Excludes tablets.
    *
    * @return int
    */
   function DetectBlackBerry10Phone()
   {
       if ((stripos($this->useragent, $this->deviceBB10) > -1) &&
          (stripos($this->useragent, $this->mobile) > -1))
         return $this->true;
       else
         return $this->false;
   }

   /**
    * Detects if the current browser is on a BlackBerry tablet device.
    * Examples: PlayBook
    *
    * @return int
    */
   function DetectBlackBerryTablet()
   {
      if ((stripos($this->useragent, $this->deviceBBPlaybook) > -1))
         return $this->true;
      else
        return $this->false;
   }

   /**
    * Detects if the current browser is a BlackBerry phone device AND uses a
    * WebKit-based browser. These are signatures for the new BlackBerry OS 6.
    * Examples: Torch. Includes the Playbook.
    *
    * @return int
    */
   function DetectBlackBerryWebKit()
   {
      if (($this->DetectBlackBerry() == $this->true) &&
		($this->DetectWebkit() == $this->true))
         return $this->true;
      else
        return $this->false;
   }

   /**
    * Detects if the current browser is a BlackBerry Touch phone device with
    * a large screen, such as the Storm, Torch, and Bold Touch. Excludes the Playbook.
    *
    * @return int
    */
   function DetectBlackBerryTouch()
   {
       if ((stripos($this->useragent, $this->deviceBBStorm) > -1) ||
		(stripos($this->useragent, $this->deviceBBTorch) > -1) ||
		(stripos($this->useragent, $this->deviceBBBoldTouch) > -1) ||
		(stripos($this->useragent, $this->deviceBBCurveTouch) > -1))
         return $this->true;
       else
         return $this->false;
   }

   /**
    * Detects if the current browser is a BlackBerry OS 5 device AND
    * has a more capable recent browser. Excludes the Playbook.
    * Examples, Storm, Bold, Tour, Curve2
    * Excludes the new BlackBerry OS 6 and 7 browser!!
    *
    * @return int
    */
   function DetectBlackBerryHigh()
   {
      //Disambiguate for BlackBerry OS 6 or 7 (WebKit) browser
      if ($this->DetectBlackBerryWebKit() == $this->true)
         return $this->false;
      if ($this->DetectBlackBerry() == $this->true)
      {
          if (($this->DetectBlackBerryTouch() == $this->true) ||
            stripos($this->useragent, $this->deviceBBBold) > -1 ||
            stripos($this->useragent, $this->deviceBBTour) > -1 ||
            stripos($this->useragent, $this->deviceBBCurve) > -1)
          {
             return $this->true;
          }
          else
            return $this->false;
      }
      else
        return $this->false;
   }

   /**
    * Detects if the current browser is a BlackBerry device AND
    * has an older, less capable browser.
    * Examples: Pearl, 8800, Curve1.
    *
    * @return int
    */
   function DetectBlackBerryLow()
   {
      if ($this->DetectBlackBerry() == $this->true)
      {
          //Assume that if it's not in the High tier, then it's Low.
          if (($this->DetectBlackBerryHigh() == $this->true) ||
			($this->DetectBlackBerryWebKit() == $this->true))
             return $this->false;
          else
            return $this->true;
      }
      else
        return $this->false;
   }

   /**
    * Detects if the current browser is the Nokia S60 Open Source Browser.
    *
    * @return int
    */
   function DetectS60OssBrowser()
   {
      //First, test for WebKit, then make sure it's either Symbian or S60.
      if ($this->DetectWebkit() == $this->true)
      {
        if (stripos($this->useragent, $this->deviceSymbian) > -1 ||
            stripos($this->useragent, $this->deviceS60) > -1)
        {
           return $this->true;
        }
        else
           return $this->false;
      }
      else
         return $this->false;
   }

   /**
    * Detects if the current device is any Symbian OS-based device,
    * including older S60, Series 70, Series 80, Series 90, and UIQ,
    * or other browsers running on these devices.
    *
    * @return int
    */
   function DetectSymbianOS()
   {
       if (stripos($this->useragent, $this->deviceSymbian) > -1 ||
           stripos($this->useragent, $this->deviceS60) > -1 ||
           stripos($this->useragent, $this->deviceS70) > -1 ||
           stripos($this->useragent, $this->deviceS80) > -1 ||
           stripos($this->useragent, $this->deviceS90) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    *Detects if the current browser is on a PalmOS device.
    *
    * @return int
    */
   function DetectPalmOS()
   {
      //Most devices nowadays report as 'Palm', but some older ones reported as Blazer or Xiino.
      if (stripos($this->useragent, $this->devicePalm) > -1 ||
          stripos($this->useragent, $this->engineBlazer) > -1 ||
          stripos($this->useragent, $this->engineXiino) > -1)
      {
         //Make sure it's not WebOS first
         if ($this->DetectPalmWebOS() == $this->true)
            return $this->false;
         else
            return $this->true;
      }
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is on a Palm device
    * running the new WebOS.
    *
    * @return int
    */
   function DetectPalmWebOS()
   {
      if (stripos($this->useragent, $this->deviceWebOS) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is on an HP tablet running WebOS.
    *
    * @return int
    */
   function DetectWebOSTablet()
   {
      if ((stripos($this->useragent, $this->deviceWebOShp) > -1)
			&& (stripos($this->useragent, $this->deviceTablet) > -1))
         return $this->true;
      else
         return $this->false;
   }
   //**************************
   // Detects if the current browser is on a WebOS smart TV.

   /**
    * Detects if the current browser is on a WebOS smart TV.
    *
    * @return int
    */
   function DetectWebOSTV()
   {
      if ((stripos($this->useragent, $this->deviceWebOStv) > -1)
			&& (stripos($this->useragent, $this->smartTV2) > -1))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is Opera Mobile or Mini.
    *
    * @return int
    */
   function DetectOperaMobile()
   {
      if ((stripos($this->useragent, $this->engineOpera) > -1) &&
      ((stripos($this->useragent, $this->mini) > -1) ||
          (stripos($this->useragent, $this->mobi) > -1)))
            return $this->true;
      return $this->false;
   }

   /**
    * Detects if the current device is an Amazon Kindle (eInk devices only).
    * Note: For the Kindle Fire, use the normal Android methods.
    *
    * @return int
    */
   function DetectKindle()
   {
      if (stripos($this->useragent, $this->deviceKindle) > -1 &&
          $this->DetectAndroid() == $this->false)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current Amazon device has turned on the Silk accelerated browsing feature.
    * v
    *
    * @return int
    */
   function DetectAmazonSilk()
   {
      if (stripos($this->useragent, $this->engineSilk) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if a Garmin Nuvifone device.
    *
    * @return int
    */
   function DetectGarminNuvifone()
   {
      if (stripos($this->useragent, $this->deviceNuvifone) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a device running the Bada OS from Samsung.
    *
    * @return int
    */
   function DetectBada()
   {
      if (stripos($this->useragent, $this->deviceBada) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a device running the Tizen smartphone OS.
    *
    * @return int
    */
   function DetectTizen()
   {
      if ((stripos($this->useragent, $this->deviceTizen) > -1)
			&& (stripos($this->useragent, $this->mobile) > -1))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is on a Tizen smart TV.
    *
    * @return int
    */
   function DetectTizenTV()
   {
      if ((stripos($this->useragent, $this->deviceTizen) > -1)
			&& (stripos($this->useragent, $this->smartTV1) > -1))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a device running the Meego OS.
    *
    * @return int
    */
   function DetectMeego()
   {
      if (stripos($this->useragent, $this->deviceMeego) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a phone running the Meego OS.
    *
    * @return int
    */
   function DetectMeegoPhone()
   {
      if ((stripos($this->useragent, $this->deviceMeego) > -1)
			&& (stripos($this->useragent, $this->mobi) > -1))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a mobile device (probably) running the Firefox OS.
    *
    * @return int
    */
   function DetectFirefoxOS()
   {
      if (($this->DetectFirefoxOSPhone() == $this->true)
		|| ($this->DetectFirefoxOSTablet() == $this->true))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a phone (probably) running the Firefox OS.
    *
    * @return int
    */
   function DetectFirefoxOSPhone()
   {
   	  //First, let's make sure we're NOT on another major mobile OS.
   	  if ($this->DetectIos() == $this->true
   	  	|| $this->DetectAndroid() == $this->true
   	  	|| $this->DetectSailfish() == $this->true)
         return $this->false;

      if ((stripos($this->useragent, $this->engineFirefox) > -1) &&
		(stripos($this->useragent, $this->mobile) > -1))
         return $this->true;

      return $this->false;
   }

   /**
    * Detects a tablet (probably) running the Firefox OS.
    *
    * @return int
    */
   function DetectFirefoxOSTablet()
   {
   	  //First, let's make sure we're NOT on another major mobile OS.
   	  if ($this->DetectIos() == $this->true
   	  	|| $this->DetectAndroid() == $this->true
   	  	|| $this->DetectSailfish() == $this->true)
         return $this->false;

      if ((stripos($this->useragent, $this->engineFirefox) > -1) &&
		(stripos($this->useragent, $this->deviceTablet) > -1))
         return $this->true;

      return $this->false;
   }

   /**
    * Detects a device running the Sailfish OS.
    *
    * @return int
    */
   function DetectSailfish()
   {
      if (stripos($this->useragent, $this->deviceSailfish) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a phone running the Sailfish OS.
    *
    * @return int
    */
   function DetectSailfishPhone()
   {
      if (($this->DetectSailfish() == $this->true) &&
		(stripos($this->useragent, $this->mobile) > -1))
         return $this->true;

      return $this->false;
   }

   /**
    * Detects a mobile device running the Ubuntu Mobile OS.
    *
    * @return int
    */
   function DetectUbuntu()
   {
      if (($this->DetectUbuntuPhone() == $this->true)
		|| ($this->DetectUbuntuTablet() == $this->true))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects a phone running the Ubuntu Mobile OS.
    *
    * @return int
    */
   function DetectUbuntuPhone()
   {
      if ((stripos($this->useragent, $this->deviceUbuntu) > -1) &&
		(stripos($this->useragent, $this->mobile) > -1))
         return $this->true;

      return $this->false;
   }

   /**
    * Detects a tablet running the Ubuntu Mobile OS.
    * Examples: PlayBook
    *
    * @return int
    */
   function DetectUbuntuTablet()
   {
      if ((stripos($this->useragent, $this->deviceUbuntu) > -1) &&
		(stripos($this->useragent, $this->deviceTablet) > -1))
         return $this->true;

      return $this->false;
   }

   /**
    * Detects the Danger Hiptop device.
    *
    * @return int
    */
   function DetectDangerHiptop()
   {
      if (stripos($this->useragent, $this->deviceDanger) > -1 ||
          stripos($this->useragent, $this->deviceHiptop) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current browser is a Sony Mylo device.
    *
    * @return int
    */
   function DetectSonyMylo()
   {
      if ((stripos($this->useragent, $this->manuSony) > -1) &&
         ((stripos($this->useragent, $this->qtembedded) > -1) ||
          (stripos($this->useragent, $this->mylocom2) > -1)))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device is on one of the Maemo-based Nokia Internet Tablets.
    *
    * @return int
    */
   function DetectMaemoTablet()
   {
      if (stripos($this->useragent, $this->maemo) > -1)
         return $this->true;
      //For Nokia N810, must be Linux + Tablet, or else it could be something else.
      if ((stripos($this->useragent, $this->linux) > -1)
		&& (stripos($this->useragent, $this->deviceTablet) > -1)
		&& ($this->DetectWebOSTablet() == $this->false)
		&& ($this->DetectAndroid() == $this->false))
         return $this->true;
      else
         return $this->false;
   }
   /**
    * Detects if the current device is an Archos media player/Internet tablet.
    *
    * @return int
    */
   function DetectArchos()
   {
      if (stripos($this->useragent, $this->deviceArchos) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device is an Internet-capable game console.
    * Includes many handheld consoles.
    *
    * @return int
    */
   function DetectGameConsole()
   {
      if ($this->DetectSonyPlaystation() == $this->true)
         return $this->true;
      else if ($this->DetectNintendo() == $this->true)
         return $this->true;
      else if ($this->DetectXbox() == $this->true)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device is a Sony Playstation.
    *
    * @return int
    */
   function DetectSonyPlaystation()
   {
      if (stripos($this->useragent, $this->devicePlaystation) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device is a handheld gaming device with
    * a touchscreen and modern iPhone-class browser. Includes the Playstation Vita.
    *
    * @return int
    */
   function DetectGamingHandheld()
   {
      if ((stripos($this->useragent, $this->devicePlaystation) > -1) &&
         (stripos($this->useragent, $this->devicePlaystationVita) > -1))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device is a Nintendo game device.
    *
    * @return int
    */
   function DetectNintendo()
   {
      if (stripos($this->useragent, $this->deviceNintendo) > -1 ||
           stripos($this->useragent, $this->deviceWii) > -1 ||
           stripos($this->useragent, $this->deviceNintendoDs) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    *Detects if the current device is a Microsoft Xbox.
    *
    * @return int
    */
   function DetectXbox()
   {
      if (stripos($this->useragent, $this->deviceXbox) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects whether the device is a Brew-powered device.
    *
    * @return int
    */
   function DetectBrewDevice()
   {
       if (stripos($this->useragent, $this->deviceBrew) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects whether the device supports WAP or WML.
    *
    * @return int
    */
   function DetectWapWml()
   {
       if (stripos($this->httpaccept, $this->vndwap) > -1 ||
           stripos($this->httpaccept, $this->wml) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Detects if the current device supports MIDP, a mobile Java technology.
    *
    * @return int
    */
   function DetectMidpCapable()
   {
       if (stripos($this->useragent, $this->deviceMidp) > -1 ||
           stripos($this->httpaccept, $this->deviceMidp) > -1)
         return $this->true;
      else
         return $this->false;
   }

   /**
    * Check to see whether the device is *any* 'smartphone'.
    * Note: It's better to use DetectTierIphone() for modern touchscreen devices.
    *
    * @return int
    */
   function DetectSmartphone()
   {
      //Exclude duplicates from TierIphone
      if (($this->DetectTierIphone() == $this->true)
		|| ($this->DetectS60OssBrowser() == $this->true)
		|| ($this->DetectSymbianOS() == $this->true)
		|| ($this->DetectWindowsMobile() == $this->true)
		|| ($this->DetectBlackBerry() == $this->true)
		|| ($this->DetectMeegoPhone() == $this->true)
		|| ($this->DetectPalmWebOS() == $this->true))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * The quick way to detect for a mobile device.
    * Will probably detect most recent/current mid-tier Feature Phones
    * as well as smartphone-class devices. Excludes Apple iPads and other modern tablets.
    *
    * @return int
    */
   function DetectMobileQuick()
   {
      if ($this->initCompleted == $this->true ||
          $this->isMobilePhone == $this->true)
         return $this->isMobilePhone;
      //Let's exclude tablets
      if ($this->isTierTablet == $this->true)
         return $this->false;

      //Most mobile browsing is done on smartphones
      if ($this->DetectSmartphone() == $this->true)
         return $this->true;
      //Catch-all for many mobile devices
      if (stripos($this->useragent, $this->mobile) > -1)
         return $this->true;
      if ($this->DetectOperaMobile() == $this->true)
         return $this->true;
      //We also look for Kindle devices
      if ($this->DetectKindle() == $this->true ||
         $this->DetectAmazonSilk() == $this->true)
         return $this->true;
      if (($this->DetectWapWml() == $this->true)
      		|| ($this->DetectMidpCapable() == $this->true)
			|| ($this->DetectBrewDevice() == $this->true))
         return $this->true;

      if ((stripos($this->useragent, $this->engineNetfront) > -1)
			|| (stripos($this->useragent, $this->engineUpBrowser) > -1))
         return $this->true;

      return $this->false;
   }

   /**
    * The longer and more thorough way to detect for a mobile device.
    * Will probably detect most feature phones,
    * smartphone-class devices, Internet Tablets,
    * Internet-enabled game consoles, etc.
    * This ought to catch a lot of the more obscure and older devices, also --
    * but no promises on thoroughness!
    *
    * @return int
    */
   function DetectMobileLong()
   {
      if ($this->DetectMobileQuick() == $this->true)
         return $this->true;
      if ($this->DetectGameConsole() == $this->true)
         return $this->true;

      if (($this->DetectDangerHiptop() == $this->true)
			|| ($this->DetectMaemoTablet() == $this->true)
			|| ($this->DetectSonyMylo() == $this->true)
			|| ($this->DetectArchos() == $this->true))
         return $this->true;
       if ((stripos($this->useragent, $this->devicePda) > -1) &&
		 !(stripos($this->useragent, $this->disUpdate) > -1))
         return $this->true;

       //Detect older phones from certain manufacturers and operators.
       if ((stripos($this->useragent, $this->uplink) > -1)
			|| (stripos($this->useragent, $this->engineOpenWeb) > -1)
       		|| (stripos($this->useragent, $this->manuSamsung1) > -1)
       		|| (stripos($this->useragent, $this->manuSonyEricsson) > -1)
       		|| (stripos($this->useragent, $this->manuericsson) > -1)
       		|| (stripos($this->useragent, $this->svcDocomo) > -1)
       		|| (stripos($this->useragent, $this->svcKddi) > -1)
       		|| (stripos($this->useragent, $this->svcVodafone) > -1))
         return $this->true;
      return $this->false;
   }

   /**
    *
    * For Mobile Web Site Design
    * The quick way to detect for a tier of devices.
    * This method detects for the new generation of
    * HTML 5 capable, larger screen tablets.
    * Includes iPad, Android (e.g., Xoom), BB Playbook, WebOS, etc.
    *
    * @return int
    */
   function DetectTierTablet()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierTablet == $this->true)
         return $this->isTierTablet;
      if (($this->DetectIpad() == $this->true)
         || ($this->DetectAndroidTablet() == $this->true)
         || ($this->DetectBlackBerryTablet() == $this->true)
         || ($this->DetectFirefoxOSTablet() == $this->true)
         || ($this->DetectUbuntuTablet() == $this->true)
         || ($this->DetectWebOSTablet() == $this->true))
         return $this->true;
      else
         return $this->false;
   }

   /**
    * The quick way to detect for a tier of devices.
    * This method detects for devices which can
    * display iPhone-optimized web content.
    * Includes iPhone, iPod Touch, Android, Windows Phone, BB10, Playstation Vita, etc.
    *
    * @return int
    */
   function DetectTierIphone()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierIphone == $this->true)
         return $this->isTierIphone;
      if (($this->DetectIphoneOrIpod() == $this->true)
			|| ($this->DetectAndroidPhone() == $this->true)
			|| ($this->DetectWindowsPhone() == $this->true)
			|| ($this->DetectBlackBerry10Phone() == $this->true)
			|| ($this->DetectPalmWebOS() == $this->true)
			|| ($this->DetectBada() == $this->true)
			|| ($this->DetectTizen() == $this->true)
			|| ($this->DetectFirefoxOSPhone() == $this->true)
			|| ($this->DetectSailfishPhone() == $this->true)
			|| ($this->DetectUbuntuPhone() == $this->true)
			|| ($this->DetectGamingHandheld() == $this->true))
         return $this->true;

      //Note: BB10 phone is in the previous paragraph
      if (($this->DetectBlackBerryWebKit() == $this->true) &&
		($this->DetectBlackBerryTouch() == $this->true))
         return $this->true;

      else
         return $this->false;
   }

   /**
    * The quick way to detect for a tier of devices.
    * This method detects for devices which are likely to be capable
    * of viewing CSS content optimized for the iPhone,
    * but may not necessarily support JavaScript.
    * Excludes all iPhone Tier devices.
    *
    * @return int
    */
   function DetectTierRichCss()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierRichCss == $this->true)
         return $this->isTierRichCss;
      if ($this->DetectMobileQuick() == $this->true)
      {
        //Exclude iPhone Tier and e-Ink Kindle devices
        if (($this->DetectTierIphone() == $this->true) ||
            ($this->DetectKindle() == $this->true))
           return $this->false;

        //The following devices are explicitly ok.
        if ($this->DetectWebkit() == $this->true) //Any WebKit
           return $this->true;
        if ($this->DetectS60OssBrowser() == $this->true)
           return $this->true;

        //Note: 'High' BlackBerry devices ONLY
        if ($this->DetectBlackBerryHigh() == $this->true)
           return $this->true;

        //Older Windows 'Mobile' isn't good enough for iPhone Tier.
        if ($this->DetectWindowsMobile() == $this->true)
           return $this->true;
        if (stripos($this->useragent, $this->engineTelecaQ) > -1)
           return $this->true;

        //default
        else
           return $this->false;
      }
      else
         return $this->false;
   }
   /**
    * The quick way to detect for a tier of devices.
    * This method detects for all other types of phones,
    * but excludes the iPhone and RichCSS Tier devices.
    *
    * @return int
    */
   function DetectTierOtherPhones()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierGenericMobile == $this->true)
         return $this->isTierGenericMobile;
      //Exclude devices in the other 2 categories
      if (($this->DetectMobileLong() == $this->true)
		&& ($this->DetectTierIphone() == $this->false)
		&& ($this->DetectTierRichCss() == $this->false))
           return $this->true;
      else
         return $this->false;
   }

}

