# Percy Rover Image Grabber
This is a really rough script written in PHP.  
It's probably poorly written, but it does work.. as tested on a Raspberry Pi 4B  
Will update the code when I get more free time.

Note that you should use the [Mars2020 site](https://mars.nasa.gov/mars2020/multimedia/raw-images/) while choosing what you want to download, as this doesn't grab what options are avaliable (yet?).  
And should be seen as a download aid rather than a replacement.

## Syntax:
~~`php mars2020.php CAM-Select raw/color sol#`~~

`php mars2020.php (Cam-select, raw/color, sol#)`

e.g: `php mars2020.php raw HNAV sol259`

CAM-Select, replace with any of the following short codes:
* RDLC - Rover Down-Look Camera
* RULC - Rover Up-Look Camera
* DDLC - Descent Stage Down-Look Camera
* PULCB - Parachute Up-Look Camera B
* PULCA - Parachute Up-Look Camera A
* MZR - Mastcam-Z-Right
* MZL - Mastcam-Z-Left
* RHR - Rear Hazcam Right
* RHL - Rear Hazcam Left
* FHR - Front Hazcam Right
* FHL - Front Hazcam Left
* NCR - Navigation Camera Right
* NCL - Navigation Camera Left

> New Cameras added Nov 13 2021

* SKYC - MEDIA SkyCam
* PIXL - PIXL Micro Context Camera
* SWAT - SHERLOC - WATSON
* SIMGR - SHERLOC Contect Imager
* SCMI - SuperCam Remote Micro Imager
* LVSC - Lander Vision System Camera
* SCS - Sample Caching System (CacheCam)
* HNAV - Navigation Camera
* HCOL - Color Camera

I tried to make the camera codes as simple as possible, if you have any suggestions for clarity / simplicity please let me know in [Issues](https://github.com/Jakesta13/Percy_Rover_Image_Grab/issues) and I'll gladly take a look.
## Notes:
* raw/color is the processing mode, raw is default if not defined.
* sol# is the sol you are requesting images from.
* You can specifiy any argument in any postion, all are optional.

~~_NOTE: You must specifiy raw/color if you are trying to select a sol, at the time being._~~

https://mars.nasa.gov/mars2020/multimedia/raw-images/

## To-do:
* Allow more flexibility with options (E.g select images per page if possible, and custom page count)
* ~~Allow arguments in any order by using --option-name (option)~~ - DONE!
* ~~Check SOL if it matches the requested SOL date, otherwise the RSS feed returns all SOLS for requested camera .. oof~~  - DONE!
* Add a way to select ALL cameras (Good for getting all of the latest SOL images!).
* Learn more PHP.
