<?php
namespace MandarinMoment;

class UpdateWord {

  /**
  * The calculated due date for the next review of the word
  *
  * @var DateTime
  */

  private $due_date;

  /**
  * The easiness factor of the word, adjusted according to performance
  *
  * @var float
  */

  private $ef;

  /**
  * The calculated number of days for next review
  *
  * @var integer
  */

  private $user_interval;

  /**
  * The number of times the word has been reviewed
  *
  * @var integer
  */

  private $n;

  /**
  * The score out of 5 from learning/review session for that word
  *
  * @var integer
  */

  private $q;

  /**
  * The unique id of word, this can be used for updating the unique database record
  *
  * @var integer
  */

  private $id;

  /**
  * The timestamp for when the learning/review session of word is complete
  *
  * @var DateTime
  */

  private $completed;

  /**
  * The constructor takes all previous property values(or default values if first time) for the word
  * and sets the current properties for the object
  *
  */

  public function __construct($due_date, $ef, $user_interval, $n, $q, $id, $timestamp) {
      $this->due_date = $due_date;
      $this->ef = $ef;
      $this->user_interval = $user_interval;
      $this->n = $n;
      $this->q = $q;
      $this->id = $id;
      $this->completed = $timestamp;
  }

  /**
  * Getters are available for all private properties
  *
  *
  */

  public function getDueDate() {
      return $this->due_date;
  }

  public function getEf() {
      return $this->ef;
  }

  public function getUserInterval() {
      return $this->user_interval;
  }

  public function getN() {
      return $this->n;
  }

  public function getQ() {
      return $this->q;
  }

  public function getId() {
      return $this->id;
  }

  public function getCompleted() {
      return $this->completed;
  }

  /**
  * Update method updates properties set via the constructor using the super memo 2 algorithm
  *
  * @return void
  */

  public function update() {

      //The new easiness factor(ef) for the word is calculated using the q value from
      //the completed learn/review session and the previous ef value. These
      //are inserted in the formula shown below
      $newEf = $this->ef + (0.1 - (5 - $this->q) * (0.08 + (5 - $this->q) * 0.02));

      //If the newly calculated ef is less than 1.3, it defaults to 1.3
      //This prevents the word needing to be reviewed too frequently
      if($newEf < 1.3) {
        $newEf = 1.3;
      }

      //If the newly calculated ef is more than 2.5, it defaults to 2.5
      //This prevents the word from not being reviewed frequently enough
      if($newEf > 2.5) {
        $newEf = 2.5;
      }

      //Once the new ef is calculated, the new interval which is the number of
      //days before its next review is calculated using the previous Interval
      //for the word and the new ef
      $newInterval = ceil($this->user_interval * $newEf);

      //N represents the number of times the word has been reviewed or learnt
      //This uses the previous value and simply increments it
      $newN = $this->n + 1;

      //After the first session, the user will always have to review the word
      //the next day
      if($newN == 1) {
        $newInterval = 1;
      }

      //After the second session, the user will always have to review the word
      //six days later
      if($newN == 2) {
        $newInterval = 6;
      }

      //If the user scores below 3/5 after the session, the review cycle has been reset
      //as if the word is going to be learnt for the first time again
      if($this->q < 3) {
        $newN = 0;
        $newInterval = 1;
      }

      //The ef, n and user_interval values are overwritten with newly calculated values
      $this->ef = $newEf;
      $this->n = $newN;
      $this->user_interval = $newInterval;

      //Timestamp passed to constructor after session is completed is used with
      //the calculated interval to determine the due date
      $timestamp = $this->completed;
      $timestamp += 60 * 60 * 24 * $this->user_interval;
      $this->due_date = $timestamp;

      //All properties are then available using the getters if values are needed to update a
      //database or any other forms of storage

  }

}
?>
