import React from 'react'
import { motion } from 'framer-motion'
import { 
  Users, 
  Target, 
  Heart, 
  Award,
  TrendingUp,
  CheckCircle,
  Calendar,
  Building
} from 'lucide-react'

const About: React.FC = () => {
  // 公司历程数据
  const milestones = [
    {
      year: '2014',
      title: '公司成立',
      description: '上海阔文展览展示服务有限公司正式成立，开始专业展台设计搭建服务'
    },
    {
      year: '2016',
      title: '业务拓展',
      description: '业务范围扩展至华东地区，团队规模达到20人'
    },
    {
      year: '2018',
      title: '技术升级',
      description: '引入3D设计技术和VR展示系统，提升设计表现力'
    },
    {
      year: '2020',
      title: '数字化转型',
      description: '完成数字化转型，建立在线设计平台和客户管理系统'
    },
    {
      year: '2022',
      title: '行业领先',
      description: '成为上海地区知名的展览服务商，累计服务客户超过500家'
    },
    {
      year: '2024',
      title: '持续创新',
      description: '不断创新服务模式，致力于为客户提供更优质的展览解决方案'
    }
  ]

  // 团队成员数据
  const team = [
    {
      name: '张总',
      position: '总经理',
      description: '15年展览行业经验，曾服务多家知名企业',
      image: '/images/team-meeting-1.jpg'
    },
    {
      name: '李总监',
      position: '设计总监',
      description: '资深设计师，擅长创意展台设计',
      image: '/images/design-process-1.jpg'
    },
    {
      name: '王经理',
      position: '项目经理',
      description: '专业项目管理，确保项目顺利实施',
      image: '/images/office-workspace-1.png'
    }
  ]

  // 企业文化数据
  const culture = [
    {
      icon: Target,
      title: '专业',
      description: '专业的团队，专业的服务，为客户提供最优质的展览解决方案'
    },
    {
      icon: Heart,
      title: '用心',
      description: '用心对待每一个项目，用心服务每一位客户'
    },
    {
      icon: TrendingUp,
      title: '创新',
      description: '不断创新设计理念和服务模式，引领行业发展'
    },
    {
      icon: Users,
      title: '合作',
      description: '与客户建立长期合作关系，共同成长发展'
    }
  ]

  // 资质荣誉数据
  const honors = [
    {
      icon: Award,
      title: '优秀展览服务商',
      year: '2023',
      organization: '上海展览行业协会'
    },
    {
      icon: Building,
      title: '诚信企业认证',
      year: '2022',
      organization: '上海市工商联'
    },
    {
      icon: CheckCircle,
      title: 'ISO9001质量认证',
      year: '2021',
      organization: '国际标准化组织'
    },
    {
      icon: TrendingUp,
      title: '高新技术企业',
      year: '2020',
      organization: '科技部认定'
    }
  ]

  return (
    <div className="bg-white">
      {/* 页面头部 */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
          style={{ backgroundImage: 'url(/images/office-workspace-1.png)' }}
        ></div>
        <div className="relative container mx-auto px-4 text-center">
          <motion.h1 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="text-4xl lg:text-5xl font-bold mb-6"
          >
            关于阔文展览
          </motion.h1>
          <motion.p 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-xl text-blue-100 max-w-3xl mx-auto"
          >
            专业展台设计搭建服务商，致力于为企业提供一站式展览解决方案
          </motion.p>
        </div>
      </section>

      {/* 公司简介 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
            >
              <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                我们的故事
              </h2>
              <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                上海阔文展览展示服务有限公司成立于2014年，是一家专业从事展台设计搭建的综合性服务企业。
                自成立以来，我们始终坚持"专业、用心、创新、合作"的企业理念，为客户提供高品质的展览服务。
              </p>
              <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                经过十年的发展，我们已经成为上海地区知名的展览服务商，拥有专业的设计团队和施工队伍，
                服务范围涵盖展台设计、展台搭建、展会策划、设备租赁等多个领域。
              </p>
              <p className="text-lg text-gray-600 mb-8 leading-relaxed">
                我们的客户遍布科技、汽车、医疗、教育、金融等多个行业，累计服务企业超过500家，
                以专业的服务和优质的品质赢得了客户的信赖和好评。
              </p>
              
              <div className="grid grid-cols-2 gap-6">
                <div className="text-center p-4 bg-blue-50 rounded-lg">
                  <div className="text-3xl font-bold text-blue-600 mb-2">500+</div>
                  <div className="text-gray-600">服务客户</div>
                </div>
                <div className="text-center p-4 bg-orange-50 rounded-lg">
                  <div className="text-3xl font-bold text-orange-600 mb-2">10+</div>
                  <div className="text-gray-600">年行业经验</div>
                </div>
              </div>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
              className="relative"
            >
              <img 
                src="/images/exhibition-hall-1.jpg" 
                alt="阔文展览办公环境"
                className="rounded-2xl shadow-2xl w-full"
              />
            </motion.div>
          </div>
        </div>
      </section>

      {/* 公司历程 */}
      <section className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              发展历程
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              十年专注，见证每一个重要时刻
            </motion.p>
          </div>

          <div className="relative">
            {/* 时间线 */}
            <div className="absolute left-1/2 transform -translate-x-1/2 w-1 bg-blue-200 h-full"></div>
            
            <div className="space-y-12">
              {milestones.map((milestone, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className={`flex items-center ${index % 2 === 0 ? 'lg:flex-row' : 'lg:flex-row-reverse'}`}
                >
                  <div className={`flex-1 ${index % 2 === 0 ? 'lg:pr-8 lg:text-right' : 'lg:pl-8'}`}>
                    <div className="bg-white p-6 rounded-lg shadow-lg">
                      <div className="text-2xl font-bold text-blue-600 mb-2">{milestone.year}</div>
                      <h3 className="text-xl font-bold text-gray-900 mb-2">{milestone.title}</h3>
                      <p className="text-gray-600">{milestone.description}</p>
                    </div>
                  </div>
                  
                  <div className="flex-shrink-0 relative">
                    <div className="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center z-10 relative">
                      <Calendar className="w-6 h-6 text-white" />
                    </div>
                  </div>
                  
                  <div className="flex-1"></div>
                </motion.div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* 团队介绍 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              核心团队
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              经验丰富的专业团队，为您提供优质服务
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {team.map((member, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300"
              >
                <img 
                  src={member.image} 
                  alt={member.name}
                  className="w-full h-48 object-cover"
                />
                <div className="p-6">
                  <h3 className="text-xl font-bold text-gray-900 mb-2">{member.name}</h3>
                  <div className="text-blue-600 font-semibold mb-3">{member.position}</div>
                  <p className="text-gray-600">{member.description}</p>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* 企业文化 */}
      <section className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              企业文化
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              我们的价值观和行为准则
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {culture.map((item, index) => {
              const IconComponent = item.icon
              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className="bg-white rounded-xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow duration-300"
                >
                  <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <IconComponent className="w-8 h-8 text-blue-600" />
                  </div>
                  <h3 className="text-xl font-bold text-gray-900 mb-4">{item.title}</h3>
                  <p className="text-gray-600">{item.description}</p>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>

      {/* 资质荣誉 */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <motion.h2 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              viewport={{ once: true }}
              className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4"
            >
              资质荣誉
            </motion.h2>
            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              viewport={{ once: true }}
              className="text-lg text-gray-600 max-w-2xl mx-auto"
            >
              专业资质认证，品质服务保障
            </motion.p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {honors.map((honor, index) => {
              const IconComponent = honor.icon
              return (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.6, delay: index * 0.1 }}
                  viewport={{ once: true }}
                  className="bg-white rounded-xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100"
                >
                  <div className="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <IconComponent className="w-6 h-6 text-orange-600" />
                  </div>
                  <h3 className="text-lg font-bold text-gray-900 mb-2">{honor.title}</h3>
                  <div className="text-orange-600 font-semibold mb-1">{honor.year}</div>
                  <p className="text-sm text-gray-600">{honor.organization}</p>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>
    </div>
  )
}

export default About
