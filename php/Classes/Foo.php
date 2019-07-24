<?php

namespace Justin\OOP;

require_once("autoload.php");
require_once(dirname(__DIR__)."/vendor/autoload.php");
use Ramsey\Uuid\Uuid;

/**
 * Creates an object for the author. Inserts authorId, authorAvatarUrl,
 * authorActivationToken, authorEmail, authorHash, and authorUsername.
 *
 * @author Justin Murphy <jmurphy33@cnm.edu>
 *
 **/

class Author implements \JsonSerializable {
	use ValidateDate;
	use ValidateUuid;

	/**
	 * Primary key === authorId
	 **/
	private $authorId;

	/**
	 *URL for author
	 **/

	private $authorAvatarUrl;

	/**
	 *Activation token for author
	 **/

	private $authorActivationToken;

	/**
	 *author email
	 **/

	private $authorEmail;

	/**
	 *Hash for author
	 **/

	private $authorHash;

	/**
	 *author's username
	 **/

	private $authorUsername;

	/**
	 *constructor
	 *
	 * @param string|Uuid $authorId id for the author
	 * @param string $newAuthorAvatarUrl
	 * @param string $newAuthorActivationToken for security
	 * @param string $newAuthorEmail for email storage
	 * @param string $newAuthorHash for hashed password storage
	 * @param string $newAuthorUsername for storing the user name
	 * @throws \RangeException if entries are too long
	 * @throws \InvalidArgumentException if email address format is incorrect
	 * @throws \Exception if any other exception is found
	 * @throws \TypeError if data entered does not meet type requirements
	 **/

	public function __construct($newAuthorId, $newAuthorAvatarUrl, $newAuthorActivationToken, $newAuthorEmail,string $newAuthorHash, $newAuthorUsername) {
		try {
				$this->setAuthorId($newAuthorId);
				$this->setAuthorAvatarUrl($newAuthorAvatarUrl);
				$this->setAuthorActivationToken($newAuthorActivationToken);
				$this->setAuthorEmail($newAuthorEmail);
				$this->setAuthorHash($newAuthorHash);
				$this->setAuthorUsername($newAuthorUsername);
		}
		catch(\RangeException | \InvalidArgumentException | \Exception $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

	}

	/**
	 *accessor method for authorId
	 *
	 * @return Uuid for author
	 **/

	public function getAuthorId() : Uuid {
		return($this->authorId);
	}

	/**
	 *mutator for authorId
	 *
	 * @param Uuid | string $newAuthorId new value of author id
	 * @throws \TypeError if $newAuthorId is not a UUID or string
	 * @throws \Exception if any other error is found
	 **/

	public function setAuthorId($newAuthorId) : void {
		try {
				$newUuid = self::validateUuid($newAuthorId);
		}	catch(\TypeError | \Exception $exception) {
				$exceptionType = get_class($exception);
				throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		//stores new uuid
		$this->authorId = $newUuid;
	}

	/**
	 *accessor method for author avatar url
	 *
	 * @return string for url
	 **/

	public function getAuthorAvatarUrl() : string {
		return ($this->authorAvatarUrl);
	}

	/**
	 *mutator for author avatar url
	 *
	 * @param string $newAuthorAvatarEmail new value of author avatar url
	 * @throws \TypeError if string entered does not meet field requirements
	 * @throws \Exception if any other error is found
	 **/

	public function setAuthorAvatarUrl(string $newAuthorAvatarUrl) : void {

		$newAuthorAvatarUrl = trim($newAuthorAvatarUrl);
		$newAuthorAvatarUrl = filter_var($newAuthorAvatarUrl, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		//verify avatar url fits in database
		if(strlen($newAuthorAvatarUrl) > 255) {
			throw(new \RangeException("Image content too large"));
		}
		//store the image
		$this->authorAvatarUrl = $newAuthorAvatarUrl;
	}

	/**
	 *accessor method for author activation token
	 *
	 * @return string for token
	 **/

	public function getAuthorActivationToken() : ?string {
		return ($this->authorActivationToken);
	}

	/**
	 *mutator for author activation token
	 *
	 * @param string $newAuthorActivationToken new value of activation token
	 * @throws \RangeException if string entered does not meet field requirements
	 * @throws \Exception if any other error is found
	 **/

	public function setAuthorActivationToken(?string $newAuthorActivationToken) : void {

		if($newAuthorActivationToken === null) {
			$this->authorActivationToken = null;
			return;
		}

		$newAuthorActivationToken = strtolower(trim($newAuthorActivationToken));
		if(ctype_xdigit($newAuthorActivationToken) === false) {
			throw(new \RangeException("Activation token not valid"));
		}

		if(strlen($newAuthorActivationToken) !== 32) {
			throw(new \RangeException("Activation token must be 32 characters"));
		}

		$this->authorActivationToken = $newAuthorActivationToken;
	}

	/**
	 *accessor method for the author email
	 *
	 * @return string for email
	 **/

	public function getAuthorEmail() : string {
		return ($this->authorEmail);
	}

	/**
	 *mutator method for author email
	 *
	 * @param string new value of email
	 * @throws \InvalidArgumentException if $newAuthorEmail is not a valid string
	 * @throws \RangeException if email is too long
	 * @throws \Exception if any other error is found	 *
	 **/

	public function setAuthorEmail(string $newAuthorEmail) : void {

		//verifies email security
		$newAuthorEmail = trim($newAuthorEmail);
		$newAuthorEmail = filter_var($newAuthorEmail, FILTER_VALIDATE_EMAIL);
		if(empty($newAuthorEmail) === true) {
			throw(new \InvalidArgumentException("email is insecure or invalid"));
		}

		//checks if email is too large for database
		if(strlen($newAuthorEmail) > 128) {
			throw(new \RangeException("email is too large"));
		}

		$this->authorEmail = $newAuthorEmail;
	}

	/**
	 * accessor method for profile hash
	 *
	 * @return value of hash
	 **/

	public function getAuthorHash() : string {
		return ($this->authorHash);
	}

	/**
	 * mutator method for authorHash
	 *
	 * @param string $newAuthorHash
	 * @throws \InvalidArgumentException if hash is not secure
	 * @throws \RangeException if hash is not proper length
	 * @throws \TypeError if hash is not a string
	 **/

	public function setAuthorHash(string $newAuthorHash) : void {

		$newAuthorHash = trim($newAuthorHash);
		if(empty($newAuthorHash) === true) {
			throw(new \InvalidArgumentException("hash field is empty or insecure"));
		}

		$hashInfo = password_get_info($newAuthorHash);
		if($hashInfo["algoName"] !== "argon2i") {
			throw(new \InvalidArgumentException("Author hash is not valid"));

		}

		if (strlen($newAuthorHash) !== 97) {
			throw(new \RangeException("Profile hash must be 97 characters"));
		}

		$this->authorHash = $newAuthorHash;
	}

	/**
	 * accessor method for authorUsername
	 *
	 * @return string for author user name
	 **/

	public function getAuthorUsername() : string {
		return ($this->authorUsername);
	}

	/**
	 * mutator method for authorUsername
	 *
	 * @param string $newAuthorUsername
	 * @throws \InvalidArgumentException if user name is not a string
	 * @throws \RangeException if new user name is longer than 32 characters
	 * @throws \TypeError if user name is not a string
	 **/

	public function setAuthorUsername(string $newAuthorUsername) : void {

		$newAuthorUsername = trim($newAuthorUsername);
		$newAuthorUsername = filter_var($newAuthorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newAuthorUsername) === true) {
			throw(new \InvalidArgumentException("username is empty or taken"));
		}

		//checks length of username
		if(strlen($newAuthorUsername) > 32) {
			throw(new \RangeException("username is too long"));
		}

		$this->authorUsername = $newAuthorUsername;
	}

	/**
	 * inserts this author into the table
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when MySQL related error occurs
	 * @throws \TypeError if $pdo is not a PDO connection
	 **/

	public function insert(\PDO $pdo) : void {

		//create query template
		$query = "INSERT INTO author(authorId, authorAvatarUrl, authorActivationToken, authorEmail, authorHash, authorUsername) 
									VALUES(:authorId, :authorAvatarUrl, :authorActivationToken, :authorEmail, :authorHash, :authorUsername)";
		$statement = $pdo -> prepare($query);

		$parameters = ["authorId" => $this -> authorId -> getBytes(), "authorAvatarUrl" => $this -> authorAvatarUrl,
								"authorActivationToken" => $this -> authorActivationToken, "authorEmail" => $this -> authorEmail,
								"authorHash" => $this -> authorHash, "authorUsername" => $this -> authorUsername];
		$statement -> execute($parameters);
	}

	/**
	 * removes this author from MySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when MySQL related error occurs
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/

	public function delete(\PDO $pdo) : void {

		//create query template
		$query = "DELETE FROM author WHERE authorId = :authorId";
		$statement = $pdo -> prepare($query);

		//bind the member variables to the place holder in the template
		$parameters = ["authorId" => $this -> authorId -> getBytes()];
		$statement ->execute($parameters);
	}

	/**
	 * Updates the author in MySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when MySQL related error occurs
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/

	public function update(\PDO $pdo) : void {

		//create query template
		$query = "UPDATE author SET authorAvatarUrl = :authorAvatarUrl, authorActivationToken = :authorActivationToken, 
						authorEmail = :authorEmail, authorHash = :authorHash, authorUsername = :authorUsername WHERE authorId = :authorId";
		$statement = $pdo ->prepare($query);

		//binds the member variables to the place holders in template
		$parameters = ["authorId" => $this -> authorId ->getBytes(), "authorAvatarUrl" => $this -> authorAvatarUrl, "authorActivationToken" => $this -> authorActivationToken,
								"authorEmail" => $this -> authorEmail, "authorHash" => $this -> authorHash, "authorUsername" => $this -> authorUsername];
		$statement ->execute($parameters);
	}

	/**
	 * Gets the author by authorId
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param $authorId author Id to search for
	 * @return author|null author or null if not found
	 * @throws \PDOException when MySQL related error occurs
	 * @throws \TypeError when a variable is not the correct data type
	 **/

	public static function getAuthorByAuthorId(\PDO $pdo) : ?Author {
		//cleans author Id before searching
		try{
			$authorId = self::validateUuid($authorId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
				throw(new \PDOException($exception -> getMessage(), 0, $exception));
		}

		//create query template
		$query = "SELECT authorId, authorAvatarUrl, authorActivationToken, authorEmail, authorHash, authorUsername FROM author
						WHERE authorId = :authorId";
		$statement = $pdo -> prepare($query);

		//binds the author Id to the place holder
		$parameters = ["authorId" => $authorId -> getBytes()];
		$statement -> execute($parameters);

		//grabs the author from MySQL
		try {
			$author = null;
			$statement -> setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement -> fetch();
			if($row !== false) {

				$author = new Author($row["authorId"], $row["authorAvatarUrl"], $row["authorActivationToken"], $row["authorEmail"], $row["authorHash"], $row["authorUsername"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($author);
	}

	/**
	 * get the author by activation token
	 *
	 * @param string $authorActivationToken
	 * @param \PDO object $pdo
	 * @return Author|null Profile or null if not found
	 * @throws \PDOException when MySQL related errors
	 * @throws \TypeError when variables are not the correct data type
	 **/

	public static function getAuthorByAuthorActivationToken(\PDO $pdo, string $authorActivationToken) : ?Author {
		//make sure activation token is in the right format and that it is a string representation of hexadecimal
		$authorActivationToken = trim($authorActivationToken);
		if(ctype_xdigit($authorActivationToken) === false) {
			throw(new \InvalidArgumentException("profile activation token is empty or in the wrong format"));
		}

		//create the query template
		$query = "SELECT authorId, authorAvatarUrl, authorActivationToken, authorEmail, authorHash, authorUsername FROM author WHERE authorActivationToken = :authorActivationToken";
		$statement = $pdo -> prepare($query);

		//bind the author activation token to the placeholder in the template
		$parameters = ["authorActivationToken" => $authorActivationToken];
		$statement -> execute($parameters);

		//grab the author form MySQL
		try{
			$author = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$author = new Author($row["authorId"], $row["authorAvatar"], $row["authorActivationToken"], $row["authorEmail"], $row["authorHash"], $row["authorUsername"]);
			}
		} catch(\Exception $exception) {
			//if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return ($author);
	}

	/**
	 *gets the author by email
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $authorEmail email to search for
	 * @return Author|null Author or null if not found
	 * @throws \PDOException when MySQL related error occurs
	 * @throws \TypeError when variables are not the correct data type
	 **/

	public static function getAuthorByAuthorEmail(\PDO $pdo, string $authorEmail): ?Author {
		//sanitize the email before searching
		$authorEmail = trim($authorEmail);
		$authorEmail = filter_var($authorEmail, FILTER_VALIDATE_EMAIL);

		if(empty($authorEmail) === true) {
			throw(new \PDOException("Not a valid email"));
		}

		//create query template
		$query = "SELECT authorId, authorAvatarUrl, authorActivationToken, authorEmail, authorHash, authorUsername FROM author WHERE authorEmail = :authorEmail";
		$statement = $pdo -> prepare($query);

		//bind the author id to the place holder in the template
		$parameters = ["authorEmail" => $authorEmail];
		$statement -> execute($parameters);

		//grab the author from MySQL
		try {
			$author = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$author = new Author($row["authorId"], $row["authorAvatarUrl"], $row["authorActivationToken"], $row["authorEmail"], $row["authorHash"], $row["authorUsername"]);
			}
		} catch(\Exception $exception) {
			//if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return ($author);
	}

	/**
	 * gets the author by username
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $authorUsername
	 * @return \SplFixedArray of all profiles found
	 * @throws \PDOException when MySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/

	public static function getAuthorByAuthorUsername(\PDO $pdo, string $authorUsername) : \SplFixedArray {
		//sanitize the username before searching
		$authorUsername = trim($authorUsername);
		$authorUsername = filter_var($authorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty)
	}




	/**
	 * Specify data which should be serialized to JSON
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
		// TODO: Implement jsonSerialize() method.
	}
}