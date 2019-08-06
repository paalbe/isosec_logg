<?

class isosec_uninstall
{
    public static function isosec_slett()
    {
        // if uninstall.php is not called by WordPress, die
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            die;
        }
        error_log("Denne funksjonen har ryddet opp");
    }
}

isosec_uninstall::isosec_slett();
