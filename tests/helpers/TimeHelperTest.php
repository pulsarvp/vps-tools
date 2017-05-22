<?php
	namespace tests\helpers;

	use Yii;
	use vps\tools\helpers\TimeHelper;
	use \yii\base\InvalidParamException;

	class TimeHelperTest extends \PHPUnit\Framework\TestCase
	{
		public function testCdate ()
		{
			$this->assertEquals(Yii::$app->formatter->asDatetime('2010-03-01 13:00:00', 'php:c'), TimeHelper::cdate('2010-03-01 13:00:00'));

			$this->expectException(InvalidParamException::class);
			TimeHelper::cdate(null);
			TimeHelper::cdate('ds8das8bdsaj');
		}

		public function testFromFrames ()
		{
			$this->assertNull(TimeHelper::fromFrames(null));
			$this->assertNull(TimeHelper::fromFrames('dsasd'));

			$this->assertEquals('00:00:00.000', TimeHelper::fromFrames(0));
			$this->assertEquals('00:00:01.800', TimeHelper::fromFrames(45));
			$this->assertEquals('00:07:02.360', TimeHelper::fromFrames(10559));
			$this->assertEquals('02:23:13.400', TimeHelper::fromFrames(214835));
			$this->assertEquals('02:23:13', TimeHelper::fromFrames(214835, 'HH:MM:SS'));
			$this->assertEquals('02:23:13:10', TimeHelper::fromFrames(214835, 221321));
		}

		public function testFromFramesToHuman ()
		{
			$this->assertNull(TimeHelper::fromFramesToHuman(null));
			$this->assertNull(TimeHelper::fromFramesToHuman('dsasd'));

			$this->assertEquals('00:00', TimeHelper::fromFramesToHuman(0));
			$this->assertEquals('00:01', TimeHelper::fromFramesToHuman(35));
			$this->assertEquals('07:02', TimeHelper::fromFramesToHuman(10559));
			$this->assertEquals('02:23:13', TimeHelper::fromFramesToHuman(214835));
			$this->assertEquals('02:23:14', TimeHelper::fromFramesToHuman(214840));

			$this->assertEquals('00:00:00', TimeHelper::fromFramesToHuman(0, true));
			$this->assertEquals('00:00:01', TimeHelper::fromFramesToHuman(35, true));
			$this->assertEquals('00:07:02', TimeHelper::fromFramesToHuman(10559, true));
			$this->assertEquals('02:23:13', TimeHelper::fromFramesToHuman(214835, true));
			$this->assertEquals('02:23:14', TimeHelper::fromFramesToHuman(214840, true));
		}

		public function testFromFramesToMs ()
		{
			$this->assertNull(TimeHelper::fromFramesToMs(null));
			$this->assertNull(TimeHelper::fromFramesToMs('dsasd'));

			$this->assertEquals(0, TimeHelper::fromFramesToMs(0));
			$this->assertEquals(1800, TimeHelper::fromFramesToMs(45));
			$this->assertEquals(422360, TimeHelper::fromFramesToMs(10559));
			$this->assertEquals(8593400, TimeHelper::fromFramesToMs(214835));
		}

		public function testFromSecondsToHuman ()
		{
			$this->assertNull(TimeHelper::fromSecondsToHuman(null));
			$this->assertNull(TimeHelper::fromSecondsToHuman('dsasd'));

			$this->assertEquals('00:00', TimeHelper::fromSecondsToHuman(0));
			$this->assertEquals('00:01', TimeHelper::fromSecondsToHuman(1.2));
			$this->assertEquals('00:02', TimeHelper::fromSecondsToHuman(1.9));
			$this->assertEquals('07:05', TimeHelper::fromSecondsToHuman(425));
			$this->assertEquals('02:23:13', TimeHelper::fromSecondsToHuman(8593));
			$this->assertEquals('02:23:13', TimeHelper::fromSecondsToHuman(8593.4));
			$this->assertEquals('02:23:14', TimeHelper::fromSecondsToHuman(8593.9));
			$this->assertEquals('02:23:14', TimeHelper::fromSecondsToHuman(8594));

			$this->assertEquals('00:00:00', TimeHelper::fromSecondsToHuman(0, true));
			$this->assertEquals('00:00:01', TimeHelper::fromSecondsToHuman(1.2, true));
			$this->assertEquals('00:00:02', TimeHelper::fromSecondsToHuman(1.9, true));
			$this->assertEquals('00:07:05', TimeHelper::fromSecondsToHuman(425, true));
			$this->assertEquals('02:23:13', TimeHelper::fromSecondsToHuman(8593, true));
			$this->assertEquals('02:23:13', TimeHelper::fromSecondsToHuman(8593.4, true));
			$this->assertEquals('02:23:14', TimeHelper::fromSecondsToHuman(8593.9, true));
			$this->assertEquals('02:23:14', TimeHelper::fromSecondsToHuman(8594, true));
		}

		public function testNow ()
		{
			// @TODO: somehow test it.
		}

		public function testToFrames ()
		{
			$this->assertNull(TimeHelper::toFrames(null));
			$this->assertNull(TimeHelper::toFrames('dsasd'));

			$this->assertEquals(0, TimeHelper::toFrames('00:00:00.000'));
			$this->assertEquals(45, TimeHelper::toFrames('00:00:01.800'));
			$this->assertEquals(37, TimeHelper::toFrames('00:00:01.12'));
			$this->assertEquals(10550, TimeHelper::toFrames('00:07:02'));
			$this->assertEquals(10559, TimeHelper::toFrames('00:07:02.360'));
			$this->assertEquals(214835, TimeHelper::toFrames('02:23:13.400'));
		}

		public function testToMs ()
		{
			$this->assertNull(TimeHelper::toMs(null));
			$this->assertNull(TimeHelper::toMs('dsasd'));

			$this->assertEquals(0, TimeHelper::toMs('00:00:00.000'));
			$this->assertEquals(1800, TimeHelper::toMs('00:00:01.800'));
			$this->assertEquals(1480, TimeHelper::toMs('00:00:01.12'));
			$this->assertEquals(422000, TimeHelper::toMs('00:07:02'));
			$this->assertEquals(422360, TimeHelper::toMs('00:07:02.360'));
			$this->assertEquals(8593400, TimeHelper::toMs('02:23:13.400'));
		}

		public function testToSeconds ()
		{
			$this->assertNull(TimeHelper::toSeconds(null));
			$this->assertNull(TimeHelper::toSeconds('dsasd'));

			$this->assertEquals(0, TimeHelper::toSeconds('00:00:00.000'));
			$this->assertEquals(2, TimeHelper::toSeconds('00:00:01.800'));
			$this->assertEquals(1, TimeHelper::toSeconds('00:00:01.12'));
			$this->assertEquals(422, TimeHelper::toSeconds('00:07:02'));
			$this->assertEquals(422, TimeHelper::toSeconds('00:07:02.360'));
			$this->assertEquals(423, TimeHelper::toSeconds('00:07:02.800'));
			$this->assertEquals(8593, TimeHelper::toSeconds('02:23:13.400'));
		}
	}