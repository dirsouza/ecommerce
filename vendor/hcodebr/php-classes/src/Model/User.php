<?php

namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Mailer;

class User extends Model {

    const SESSION = "User";
    const OPTIONS = [
        'cost' => 12
    ];
    const STRING_SECURITY = "bG9jYWwucGFwZnVsbC5icg==";
    const METHOD_ENCRYPT = "AES-128-CBC";

    public static function login($login, $password) {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ':LOGIN' => $login
        ));

        if (count($results) === 0) {

            header("location: /admin/login/error");
            exit;
        }

        $data = $results[0];

        if (password_verify($password, $data['despassword']) === true) {

            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;
        } else {

            header("location: /admin/login/error");
            exit;
        }
    }

    public static function verifyLogin($inadmin = true) {

        if (
            !isset($_SESSION[User::SESSION]) ||
            !$_SESSION[User::SESSION] ||
            !(int) $_SESSION[User::SESSION]['iduser'] > 0 ||
            (bool) $_SESSION[User::SESSION]['inadmin'] !== $inadmin
        ) {
            header("location: /admin/login");

            exit;
        }
    }

    public static function logout() {

        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll() {

        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY a.iduser");
    }

    public function save() {

        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save (:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ':desperson' => $this->getdesperson(),
            ':deslogin' => $this->getdeslogin(),
            ':despassword' => password_hash($this->getdespassword(), PASSWORD_DEFAULT, User::OPTIONS),
            ':desemail' => $this->getdesemail(),
            ':nrphone' => $this->getnrphone(),
            ':inadmin' => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function get($iduser) {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            ':iduser' => $iduser
        ));

        $this->setData($results[0]);
    }

    public function update() {

        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save (:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ':iduser' => $this->getiduser(),
            ':desperson' => $this->getdesperson(),
            ':deslogin' => $this->getdeslogin(),
            ':despassword' => $this->getdespassword(),
            ':desemail' => $this->getdesemail(),
            ':nrphone' => $this->getnrphone(),
            ':inadmin' => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function delete() {

        $sql = new Sql();

        $sql->query("CALL sp_users_delete (:iduser)", array(
            ':iduser' => $this->getiduser()
        ));
    }

    public static function getForgot($email) {

        $sql = new Sql();

        $results = $sql->select("
            SELECT * FROM tb_persons a 
            INNER JOIN tb_users b 
            USING (idperson) 
            WHERE a.desemail = :email", array(
            ':email' => $email
        ));

        if (count($results) === 0) {

            throw new \Exception("Não foi possível recuperar a senha.");
        } else {

            $dataResults = $results[0];

            $recovery = $sql->select("CALL sp_userspasswordsrecoveries_create (:iduser, :desip)", array(
                ':iduser' => $dataResults['iduser'],
                ':desip' => $_SERVER["REMOTE_ADDR"]
            ));

            if (count($recovery) === 0) {

                throw new \Exception("Não foi possível recuperar a senha.");
            } else {

                $dataRecovery = $recovery[0];

                //$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery['idrecovery'], MCRYPT_MODE_ECB));
                //$code = password_hash($dataRecovery['idrecovery'], PASSWORD_DEFAULT, User::OPTIONS);
                $code_encrypted = User::forgotEncrypt($dataRecovery['idrecovery'], User::STRING_SECURITY);

                $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code_encrypted";

                $mailer = new Mailer($dataResults['desemail'], $dataResults['desperson'], "Redefinir Senha da Hcode Store", "forgot", array(
                    'name' => $dataResults['desperson'],
                    'link' => $link
                ));

                $mailer->send();

                return $dataResults;
            }
        }
    }

    public static function forgotEncrypt($data, $key) {
        $encryption_key = base64_decode($key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(User::METHOD_ENCRYPT));
        $encrypted = openssl_encrypt($data, User::METHOD_ENCRYPT, $encryption_key, 0, $iv);

        return base64_encode($encrypted . "::" . $iv);
    }

    public static function validForgotDecrypt($code) {

        $sql = new Sql();

        $results = $sql->select("SELECT idrecovery FROM tb_userspasswordsrecoveries");

        $code_decrypted = User::forgotDecrypt($code, User::STRING_SECURITY);

        if (count($results) > 0) {

            foreach ($results as $_data) {

                foreach ($_data as $key => $value) {

                    if ($value === $code_decrypted) {

                        $user = $sql->select("
                        SELECT * FROM tb_userspasswordsrecoveries a 
                        INNER JOIN tb_users b USING(iduser) 
                        INNER JOIN tb_persons c USING(idperson) 
                        WHERE 
                            a.idrecovery = :idrecovery 
                            AND 
                            a.dtrecovery IS NULL 
                            AND 
                            DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()", array(
                            ':idrecovery' => $value
                        ));

                        if (count($user) === 0) {

                            throw new \Exception("Não foi possível recuperar a senha.");
                        } else {

                            return $user[0];
                        }
                    }
                }
            }
        }
    }

    public static function forgotDecrypt($data, $key) {
        $encryption_key = base64_decode($key);
        list($encrypted_data, $iv) = explode("::", base64_decode($data), 2);

        return openssl_decrypt($encrypted_data, User::METHOD_ENCRYPT, $encryption_key, 0, $iv);
    }

    public static function setForgotUser($idrecovery) {

        $sql = new Sql();

        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
            ':idrecovery' => $idrecovery
        ));
    }

    public function setPassword($password) {

        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
            ':password' => $password,
            ':iduser' => $this->getiduser()
        ));
    }

}
