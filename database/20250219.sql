-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2025 at 01:19 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finez_seo`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seo_backlinks`
--

CREATE TABLE `tbl_seo_backlinks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `links` varchar(200) NOT NULL,
  `content` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `login_id` int(11) NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_seo_backlinks`
--

INSERT INTO `tbl_seo_backlinks` (`id`, `project_id`, `links`, `content`, `user_name`, `password`, `login_id`, `date_time`) VALUES
(1, 1, 'https://www.prbookmarks.com/preview-story/', 5, '', '', 2, '2025-02-14 00:00:00'),
(2, 1, 'https://www.prbookmarks.com/preview-story/', 5, '', '', 2, '2025-02-14 00:00:00'),
(3, 1, 'https://www.leodirectory.com/preview-directory/', 5, '', '', 2, '2025-02-14 00:00:00'),
(4, 1, 'https://www.leodirectory.com/preview-directory/', 5, '', '', 2, '2025-02-14 00:00:00'),
(5, 1, 'https://www.seolinksubmit.com/preview-weblink/', 5, '', '', 2, '2025-02-14 00:00:00'),
(6, 1, 'https://www.submitindustry.com/preview-business/', 5, '', '', 2, '2025-02-14 00:00:00'),
(7, 1, 'https://www.submitindustry.com/preview-business/', 5, '', '', 2, '2025-02-15 00:00:00'),
(8, 1, 'https://www.bookmarkset.com/preview-news/', 5, '', '', 2, '2025-02-15 00:00:00'),
(9, 1, 'https://www.bookmarkset.com/preview-news/', 5, '', '', 2, '2025-02-15 00:00:00'),
(10, 1, 'https://www.bookmarkset.com/preview-news/', 5, '', '', 2, '2025-02-15 00:00:00'),
(11, 1, 'https://www.serviceplaces.com/preview-service/', 5, '', '', 2, '2025-02-15 00:00:00'),
(12, 1, 'https://www.seosubmitbookmark.com/preview-listing/', 5, '', '', 2, '2025-02-15 00:00:00'),
(13, 1, 'https://issuu.com/atvquadindubai/docs/experience_the_thrill_of_dubai_s_desert_with_the_b', 4, '', '', 2, '2025-02-15 00:00:00'),
(14, 1, 'https://www.slideshare.net/slideshow/pathfinder-atv-adventures-conquer-the-desert-in-style-pdf/275672273', 4, '', '', 2, '2025-02-15 00:00:00'),
(15, 1, 'https://www.slideshare.net/slideshow/experience-the-thrill-of-dubai-s-desert-with-the-best-quad-bike-rentals-unforgettable-adventures-await-1-pdf/275672272', 4, '', '', 2, '2025-02-15 00:00:00'),
(16, 1, 'https://issuu.com/atvquadindubai/docs/pathfinder_atv_adventures_conquer_the_desert_in_st', 4, '', '', 2, '2025-02-15 00:00:00'),
(17, 1, 'https://www.edocr.com/v/0yldngng/atvquadindubai/pathfinder-atv-adventures-conquer-the-desert-in-st', 4, '', '', 2, '2025-02-15 00:00:00'),
(18, 1, 'https://www.edocr.com/v/vvzozlxv/atvquadindubai/experience-the-thrill-of-dubais-desert-with-the-be', 4, '', '', 2, '2025-02-15 00:00:00'),
(19, 1, 'https://www.scribd.com/document/828293517/Pathfinder-ATV-Adventures-Conquer-the-Desert-in-Style', 4, '', '', 2, '2025-02-15 00:00:00'),
(20, 1, 'https://www.scribd.com/document/828294957/Experience-the-Thrill-of-Dubai-s-Desert-With-the-Best-Quad-Bike-Rentals-Unforgettable-Adventures-Await-1', 4, '', '', 2, '2025-02-15 00:00:00'),
(21, 1, 'https://www.4shared.com/web/preview/pdf/GIbCXEHoku?', 4, '', '', 2, '2025-02-15 00:00:00'),
(22, 1, 'https://www.4shared.com/web/preview/pdf/FAc08Exrku?', 4, '', '', 2, '2025-02-15 00:00:00'),
(23, 1, 'https://www.mediafire.com/file/7o1tuxl676bqkce/Pathfinder_ATV_Adventures_Conquer_the_Desert_in_Style.pdf/file', 4, '', '', 2, '2025-02-15 00:00:00'),
(24, 1, 'https://www.mediafire.com/file/sdr0lh0caxpaoel/Experience_the_Thrill_of_Dubai%25E2%2580%2599s_Desert_with_the_Best_Quad_Bike_Rentals_Unforgettable_Adventures_Await_%25281%2529.pdf/file', 4, '', '', 2, '2025-02-15 00:00:00'),
(25, 1, 'https://www.quora.com/Are-private-desert-safari-tours-available-in-Dubai-and-what-are-their-benefits/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-15 00:00:00'),
(26, 1, 'https://www.yumpu.com/en/document/view/69926999/desert-thunder-atv-adventure', 4, '', '', 2, '2025-02-15 00:00:00'),
(27, 1, 'https://www.slideserve.com/atvquadindubai/desert-thunder-atv-adventure', 4, '', '', 2, '2025-02-15 00:00:00'),
(28, 1, 'https://www.bookmarkcart.com/preview-listing/', 5, '', '', 2, '2025-02-15 00:00:00'),
(29, 1, 'https://www.a2ztopnews.com/preview-news/', 5, '', '', 2, '2025-02-15 00:00:00'),
(30, 1, 'https://www.globalwebmarks.com/preview-webmark/', 5, '', '', 2, '2025-02-15 00:00:00'),
(31, 1, 'https://www.techbookmarks.com/preview-bookmark/', 5, '', '', 2, '2025-02-15 00:00:00'),
(32, 1, 'https://www.socialwebmarks.com/preview-webmark/', 5, '', '', 2, '2025-02-15 00:00:00'),
(33, 1, 'https://medium.com/@atvquadindubai/exciting-atv-safari-adventures-in-the-dubai-desert-f9af2ef422f8', 11, '', '', 2, '2025-02-15 00:00:00'),
(34, 1, 'https://medium.com/@atvquadindubai/atv-quad-bike-the-adrenaline-rush-f65898be5d70', 2, '', '', 2, '2025-02-17 00:00:00'),
(35, 1, 'https://www.quora.com/How-much-does-a-Dubai-desert-safari-cost-and-what-does-it-include/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-17 00:00:00'),
(36, 1, 'https://www.quora.com/How-much-does-a-budget-friendly-desert-safari-in-Dubai-cost/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-17 00:00:00'),
(37, 1, 'https://www.quora.com/What-is-included-in-the-Desert-Safari-Dubai-tickets/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-17 00:00:00'),
(38, 1, 'https://www.quora.com/What-is-the-cost-of-Desert-Safari-Dubai-I-have-plan-a-trip-over-Dubai/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-17 00:00:00'),
(39, 1, 'https://www.quora.com/Are-group-discounts-available-for-desert-safari-bookings-in-Dubai/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-17 00:00:00'),
(40, 1, 'https://www.goarticles.trendingsblog.com/quad-bike-safari-in-abu-dubai-pathfinder-2/', 2, '', '', 2, '2025-02-17 00:00:00'),
(41, 1, 'https://www.goarticles.trendingsblog.com/atv-quad-bike-safari-in-abu-dubai/', 2, '', '', 2, '2025-02-17 00:00:00'),
(42, 1, 'https://www.slideshare.net/slideshow/explore-the-thrill-of-dubai-s-desert-with-atv-quad-adventures-pdf-c41a/275725199', 4, '', '', 2, '2025-02-17 00:00:00'),
(43, 1, 'https://violet-ellen-5.tiiny.site', 4, '', '', 2, '2025-02-17 00:00:00'),
(44, 1, 'https://www.yumpu.com/en/account/magazines/edit/69936941', 4, '', '', 2, '2025-02-17 00:00:00'),
(45, 1, 'https://www.linkedin.com/in/atv-quad-bikes-dubai-2b18132b8/?miniProfileUrn=urn%3Ali%3Afsd_profile%3AACoAAExLhuUBPDq2-_00kZAWJLwHmc3O3CPB0R0', 10, '', '', 2, '2025-02-17 00:00:00'),
(46, 1, 'https://www.linkedin.com/in/atv-quad-bikes-dubai-2b18132b8/?miniProfileUrn=urn%3Ali%3Afsd_profile%3AACoAAExLhuUBPDq2-_00kZAWJLwHmc3O3CPB0R0', 10, '', '', 2, '2025-02-17 00:00:00'),
(47, 1, 'https://www.blogger.com/blog/post/edit/3067359409921461667/564146395369690967', 11, '', '', 2, '2025-02-17 00:00:00'),
(48, 1, 'https://www.quora.com/How-far-is-a-desert-safari-from-Dubai/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-17 00:00:00'),
(49, 1, 'https://medium.com/@atvquadindubai/thrilling-adventure-path-finder-quad-bikes-df6f3a9c713d', 2, '', '', 2, '2025-02-17 00:00:00'),
(50, 1, 'https://www.linkedin.com/feed/update/urn:li:share:7297150462116466690/', 10, '', '', 2, '2025-02-17 00:00:00'),
(51, 1, 'https://www.bookmarkingpage.mgnlink.com/explore-the-thrill-of-dubais-desert-with-atv-quad-adventures/', 2, '', '', 2, '2025-02-18 00:00:00'),
(52, 1, 'https://www.bloglovin.com/@pathfinder8/explore-thrill-dubais-desert-with-atv-quad-13282994', 2, '', '', 2, '2025-02-18 00:00:00'),
(53, 1, 'https://www.quora.com/How-can-I-find-the-best-deals-for-a-budget-friendly-desert-safari-in-Dubai/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(54, 1, 'https://www.quora.com/How-can-you-find-the-best-desert-safari-Dubai-deals-for-an-unforgettable-experience/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(55, 1, 'https://www.quora.com/Can-I-book-an-affordable-desert-safari-in-Dubai-for-a-large-group/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(56, 1, 'https://www.linkedin.com/in/atv-quad-bikes-dubai-2b18132b8/', 10, '', '', 2, '2025-02-18 00:00:00'),
(57, 1, 'https://sites.google.com/d/1XBbZGhombzst-irne4xZy-0OXkk54vGv/p/102IFdwgjS423jEtOIkJC_hQzuFC4y8dQ/edit', 12, '', '', 2, '2025-02-18 00:00:00'),
(58, 1, 'https://myarticles.io/?p=160267', 2, '', '', 2, '2025-02-18 00:00:00'),
(59, 1, 'https://medium.com/@atvquadindubai/explore-the-dubai-desert-with-atv-quad-adventures-132d520475f1', 2, '', '', 2, '2025-02-18 00:00:00'),
(60, 1, 'https://www.linkedin.com/posts/atv-quad-bikes-dubai-2b18132b8_explore-the-dubai-desert-with-atv-quad-adventures-activity-7297481213257175040-UEWm?utm_source=share&utm_medium=member_desktop&rcm=ACoAAEx', 10, '', '', 2, '2025-02-18 00:00:00'),
(61, 1, 'https://www.reddit.com/r/dubai/hot/', 9, '', '', 2, '2025-02-18 00:00:00'),
(62, 1, 'https://www.reddit.com/r/dubai/comments/1is53xr/explore_the_dubai_desert_with_atv_quad_adventures/', 9, '', '', 2, '2025-02-18 00:00:00'),
(63, 1, 'https://www.scoop.it/topic/pathfinder-by-atvquadindubai-gmail-com', 2, '', '', 2, '2025-02-18 00:00:00'),
(64, 1, 'https://www.quora.com/What-activities-are-included-in-a-desert-safari-booking/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(65, 1, 'https://www.quora.com/Which-is-better-a-morning-or-evening-desert-safari-in-Jaisalmer/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(66, 1, 'https://www.quora.com/How-do-I-find-good-drop-top-rentals-in-Dubai-for-my-next-trip/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(67, 1, 'https://www.quora.com/Do-you-offer-any-discounts-or-deals-for-hotel-bookings-during-off-peak-seasons/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(68, 1, 'https://www.quora.com/Do-you-offer-any-discounts-or-deals-for-hotel-bookings-during-off-peak-seasons/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-18 00:00:00'),
(69, 1, 'https://ext-6603271.livejournal.com/1303.html?newpost=1', 2, '', '', 2, '2025-02-18 00:00:00'),
(70, 1, 'https://www.a1articles.com/news/adventure-thrills-atv-quad-bike', 2, '', '', 2, '2025-02-18 00:00:00'),
(71, 1, 'https://www.linkedin.com/feed/update/urn:li:share:7297833640225329153/', 10, '', '', 2, '2025-02-19 00:00:00'),
(72, 1, 'https://medium.com/@atvquadindubai/atv-quad-in-dubai-an-ultimate-adventure-experience-84f7ba371807', 2, '', '', 2, '2025-02-19 00:00:00'),
(73, 1, 'https://www.blogger.com/blog/post/edit/preview/3067359409921461667/9077322734755666196', 2, '', '', 2, '2025-02-19 00:00:00'),
(74, 1, 'https://ext-6603271.livejournal.com/1734.html?newpost=1', 2, '', '', 2, '2025-02-19 00:00:00'),
(75, 1, 'https://www.reddit.com/r/dubai/comments/1isy6p6/atv_quad_in_dubai_an_ultimate_adventure_experience/', 2, '', '', 2, '2025-02-19 00:00:00'),
(76, 1, 'https://www.linkedin.com/in/atv-quad-bikes-dubai-2b18132b8/', 10, '', '', 2, '2025-02-19 00:00:00'),
(77, 1, 'https://www.quora.com/Are-there-any-music-festivals-in-Jaisalmer/answer/ATV-QUAD-InDUBAI?prompt_topic_bio=1', 8, '', '', 2, '2025-02-19 00:00:00'),
(78, 1, 'https://sites.google.com/view/atv-quad-bike-dubai-pathfinder/work', 12, '', '', 2, '2025-02-19 00:00:00'),
(79, 1, 'https://www.bloglovin.com/@pathfinder8/atv-quad-in-dubai-an-ultimate-adventure-experience', 2, '', '', 2, '2025-02-19 00:00:00'),
(80, 1, 'https://www.a1articles.com/news/adventure-begins-atv-quad-bike', 2, '', '', 2, '2025-02-19 00:00:00'),
(81, 1, 'https://www.reddit.com/r/dubai/comments/1isze2t/adventure_begins_from_herepath_finderatv_quad/', 7, '', '', 2, '2025-02-19 00:00:00'),
(82, 1, 'https://www.tumblr.com/atvquadindubai/775895712598802432/atv-quad-in-dubai-an-ultimate-adventure?source=share', 2, '', '', 2, '2025-02-19 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_seo_backlinks`
--
ALTER TABLE `tbl_seo_backlinks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_seo_backlinks`
--
ALTER TABLE `tbl_seo_backlinks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
